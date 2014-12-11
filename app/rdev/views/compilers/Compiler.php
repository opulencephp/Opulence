<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines methods for compiling templates
 */
namespace RDev\Views\Compilers;
use RDev\Views;
use RDev\Views\Cache;
use RDev\Views\Filters;

class Compiler implements ICompiler
{
    /** @var array The list of custom compilers */
    protected $compilers = [
        "priority" => [],
        "nonPriority" => []
    ];
    /** @var Cache\ICache The cache to use for compiled templates */
    protected $cache = null;
    /** @var array The mapping of function names to their callbacks */
    protected $templateFunctions = [];
    /** @var Filters\IFilter The cross-site scripting filter */
    protected $xssFilter = null;

    /**
     * @param Cache\ICache $cache The cache to use for compiled templates
     * @param Filters\IFilter $xssFilter The cross-site scripting filter
     */
    public function __construct(Cache\ICache $cache, Filters\IFilter $xssFilter)
    {
        $this->setCache($cache);
        $this->setXSSFilter($xssFilter);

        // Order here matters
        $this->registerCompiler([$this, "compilePartStatements"]);
        $this->registerCompiler([$this, "compilePHP"]);
        $this->registerCompiler([$this, "compileTags"]);
        $this->registerCompiler([$this, "cleanupTags"]);
        $templateFunctionRegistrant = new BuiltInTemplateFunctionRegistrant();
        $templateFunctionRegistrant->registerTemplateFunctions($this);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(Views\ITemplate $template)
    {
        $template->prepare();
        $uncompiledContents = $template->getContents();
        $compiledContents = $this->cache->get($uncompiledContents, $template->getVars(), $template->getTags());

        if($compiledContents === null)
        {
            // Sort the compile functions by their priorities
            usort($this->compilers["priority"], [$this, "sortPriority"]);

            $compiledContents = $uncompiledContents;

            // Compile the priority compilers
            foreach($this->compilers["priority"] as $compileFunctionData)
            {
                $compiledContents = call_user_func_array($compileFunctionData["compiler"], [$template, $compiledContents]);
            }

            // Compile the non-priority compilers
            foreach($this->compilers["nonPriority"] as $compileFunction)
            {
                $compiledContents = call_user_func_array($compileFunction, [$template, $compiledContents]);
            }

            // Remember this for next time
            $this->cache->set(
                $compiledContents,
                $uncompiledContents,
                $template->getVars(),
                $template->getTags()
            );
        }

        return $compiledContents;
    }

    /**
     * {@inheritdoc}
     */
    public function executeTemplateFunction($functionName, array $args = [])
    {
        if(!isset($this->templateFunctions[$functionName]))
        {
            throw new \InvalidArgumentException("Template function \"$functionName\" does not exist");
        }

        return call_user_func_array($this->templateFunctions[$functionName], $args);
    }

    /**
     * {@inheritdoc}
     */
    public function getXSSFilter()
    {
        return $this->xssFilter;
    }

    /**
     * {@inheritdoc}
     */
    public function registerCompiler($compiler, $priority = null)
    {
        if(!is_callable($compiler, true))
        {
            throw new \InvalidArgumentException("Compiler is not callable");
        }

        if($priority === null)
        {
            $this->compilers["nonPriority"][] = $compiler;
        }
        elseif(!is_int($priority) || $priority < 1)
        {
            throw new \InvalidArgumentException("Priority must be positive integer");
        }
        else
        {
            $this->compilers["priority"][] = [
                "compiler" => $compiler,
                "priority" => $priority
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function registerTemplateFunction($functionName, callable $function)
    {
        $this->templateFunctions[$functionName] = $function;
    }

    /**
     * {@inheritdoc}
     */
    public function setCache(Cache\ICache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function setXSSFilter($xssFilter)
    {
        $this->xssFilter = $xssFilter;
    }

    /**
     * Cleans up unused tags and escape characters before tags in a template
     *
     * @param Views\ITemplate $template The template whose tags we're compiling
     * @param string $content The actual content to compile
     * @return string The compiled template
     */
    private function cleanupTags(Views\ITemplate $template, $content)
    {
        // Holds the tags, with the longest-length opening tag first
        $tags = [];

        // In the case that one open tag is a substring of another (eg "{{" and "{{{"), handle the longer one first
        // If they're the same length, they cannot be substrings of one another unless they're equal
        if(strlen($template->getEscapedOpenTag()) > strlen($template->getUnescapedOpenTag()))
        {
            $tags[] = [$template->getEscapedOpenTag(), $template->getEscapedCloseTag()];
            $tags[] = [$template->getUnescapedOpenTag(), $template->getUnescapedCloseTag()];
        }
        else
        {
            $tags[] = [$template->getUnescapedOpenTag(), $template->getUnescapedCloseTag()];
            $tags[] = [$template->getEscapedOpenTag(), $template->getEscapedCloseTag()];
        }

        /**
         * The reason we cannot combine this loop and the next is that we must remove all unused tags before
         * removing their escape characters
         */
        foreach($tags as $tagsByType)
        {
            // Remove unused tags
            $content = preg_replace(
                sprintf(
                    "/(?<!%s)%s((?!%s).)*%s/",
                    preg_quote("\\", "/"),
                    preg_quote($tagsByType[0], "/"),
                    preg_quote($tagsByType[1], "/"),
                    preg_quote($tagsByType[1], "/")
                ),
                "",
                $content
            );
        }

        foreach($tags as $tagsByType)
        {
            // Remove the escape character (eg "\" from "\{{foo}}")
            $content = preg_replace(
                sprintf(
                    "/%s(%s\s*((?!%s).)*\s*%s)/U",
                    preg_quote("\\", "/"),
                    preg_quote($tagsByType[0], "/"),
                    preg_quote($tagsByType[1], "/"),
                    preg_quote($tagsByType[1], "/")
                ),
                "$1",
                $content
            );
        }

        return $content;
    }

    /**
     * Compiles PHP that appears in a template
     *
     * @param Views\ITemplate $template The template whose tags we're compiling
     * @param string $content The actual content to compile
     * @return string The compiled template
     */
    private function compilePHP(Views\ITemplate $template, $content)
    {
        // Create local variables for use in eval()
        extract($template->getVars());

        $startOBLevel = ob_get_level();
        ob_start();

        // Compile the functions
        foreach($this->templateFunctions as $functionName => $callback)
        {
            $regex = "/%s\s*%s\(\s*((?:(?!\)\s*%s).)*)\s*\)\s*%s/";
            $functionCallString = 'call_user_func_array($this->templateFunctions["' . $functionName . '"], [\1])';
            // Replace function calls in escaped tags
            $content = preg_replace(
                sprintf(
                    $regex,
                    preg_quote($template->getEscapedOpenTag(), "/"),
                    preg_quote($functionName, "/"),
                    preg_quote($template->getEscapedCloseTag(), "/"),
                    preg_quote($template->getEscapedCloseTag(), "/")),
                '<?php echo $this->xssFilter->run(' . $functionCallString . '); ?>',
                $content
            );
            // Replace function calls in unescaped tags
            $content = preg_replace(
                sprintf(
                    $regex,
                    preg_quote($template->getUnescapedOpenTag(), "/"),
                    preg_quote($functionName, "/"),
                    preg_quote($template->getUnescapedCloseTag(), "/"),
                    preg_quote($template->getUnescapedCloseTag(), "/")),
                "<?php echo $functionCallString; ?>",
                $content
            );
        }

        // Notice the little hack inside eval() to compile inline PHP
        if(eval("?>" . $content) === false)
        {
            // Prevent an invalid template from displaying
            while(ob_get_level() > $startOBLevel)
            {
                ob_end_clean();
            }

            throw new \RuntimeException("Invalid PHP inside template");
        }

        return ob_get_clean();
    }

    /**
     * Compiles part statements that appears in a template
     *
     * @param Views\ITemplate $template The template whose statements we're compiling
     * @param string $content The actual content to compile
     * @return string The compiled template
     */
    private function compilePartStatements(Views\ITemplate $template, $content)
    {
        $callback = function($matches) use ($template)
        {
            $template->setTag($matches[2], $matches[3]);

            return "";
        };
        $regex = sprintf(
            '/(?<!%s)%s\s*part\((["|\'])([^\1]+)\1\)\s*%s(.*)%s\s*endpart\s*%s/s',
            preg_quote("\\", "/"),
            preg_quote($template->getStatementOpenTag(), "/"),
            preg_quote($template->getStatementCloseTag(), "/"),
            preg_quote($template->getStatementOpenTag(), "/"),
            preg_quote($template->getStatementCloseTag(), "/")
        );
        $content = preg_replace_callback($regex, $callback, $content);

        return $content;
    }

    /**
     * Compiles tags in a template
     *
     * @param Views\ITemplate $template The template whose tags we're compiling
     * @param string $content The actual content to compile
     * @return string The compiled template
     */
    private function compileTags(Views\ITemplate $template, $content)
    {
        // Holds the tags as well as the callbacks to callbacks to execute in the case of string literals or tag names
        $tagData = [
            [
                "tags" => [$template->getEscapedOpenTag(), $template->getEscapedCloseTag()],
                "stringLiteralCallback" => function ($stringLiteral) use ($template)
                {
                    return $this->xssFilter->run(trim($stringLiteral, $stringLiteral[0]));
                },
                "tagNameCallback" => function ($tagName) use ($template)
                {
                    return $this->xssFilter->run($template->getTag($tagName));
                }
            ],
            [
                "tags" => [$template->getUnescapedOpenTag(), $template->getUnescapedCloseTag()],
                "stringLiteralCallback" => function ($stringLiteral) use ($template)
                {
                    return trim($stringLiteral, $stringLiteral[0]);
                },
                "tagNameCallback" => function ($tagName) use ($template)
                {
                    return $template->getTag($tagName);
                }
            ]
        ];

        foreach($tagData as $tagDataByType)
        {
            // Create the regexes to find escaped tags with bookends
            $arrayMapCallback = function ($tagName) use ($content, $tagDataByType)
            {
                return sprintf(
                    "/(?<!%s)%s\s*(%s)\s*%s/U",
                    preg_quote("\\", "/"),
                    preg_quote($tagDataByType["tags"][0], "/"),
                    preg_quote($tagName, "/"),
                    preg_quote($tagDataByType["tags"][1], "/")
                );
            };

            // Filter the values
            $regexCallback = function ($matches) use ($tagDataByType)
            {
                $tagName = $matches[1];

                // If the tag name is a string literal
                if(isset($tagName) && $tagName[0] == $tagName[strlen($tagName) - 1]
                    && ($tagName[0] == "'" || $tagName[0] == '"')
                )
                {
                    return call_user_func_array($tagDataByType["stringLiteralCallback"], [$tagName]);
                }

                return call_user_func_array($tagDataByType["tagNameCallback"], [$tagName]);
            };

            // Replace string literals
            $content = preg_replace_callback(
                sprintf(
                    "/(?<!%s)%s\s*((([\"'])[^\\3]*\\3))\s*%s/U",
                    preg_quote("\\", "/"),
                    preg_quote($tagDataByType["tags"][0], "/"),
                    preg_quote($tagDataByType["tags"][1], "/")
                ),
                $regexCallback,
                $content
            );

            // Replace the tags with their values
            $regexes = array_map($arrayMapCallback, array_keys($template->getTags()));
            $content = preg_replace_callback($regexes, $regexCallback, $content);
        }

        return $content;
    }

    /**
     * Sorts two arrays of compilers by comparing their priorities
     *
     * @param array $a An array containing the priority and compiler
     * @param array $b An array containing the priority and compiler
     * @return int A value suitable for a sorting function
     */
    private function sortPriority(array $a, array $b)
    {
        if($a["priority"] > $b["priority"])
        {
            return 1;
        }
        elseif($a["priority"] == $b["priority"])
        {
            return 0;
        }
        else
        {
            return -1;
        }
    }
} 
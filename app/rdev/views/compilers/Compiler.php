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

    /**
     * @param Cache\ICache $cache The cache to use for compiled templates
     */
    public function __construct(Cache\ICache $cache)
    {
        $this->setCache($cache);

        // Order here matters
        $this->registerCompiler([$this, "compilePHP"]);
        $this->registerCompiler([$this, "compileTags"]);
        $this->registerCompiler([$this, "cleanupTags"]);
        $this->registerBuiltInFunctions();
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
                "<?php echo RDev\\Views\\Filters\\XSS::run($functionCallString); ?>",
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
        if(@eval("?>" . $content) === false)
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
                    return Filters\XSS::run(trim($stringLiteral, $stringLiteral[0]));
                },
                "tagNameCallback" => function ($tagName) use ($template)
                {
                    return Filters\XSS::run($template->getTag($tagName));
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
     * Registers the built-in template function compilers
     */
    private function registerBuiltInFunctions()
    {
        // Register the absolute value function
        $this->registerTemplateFunction("abs", function ($number)
        {
            return abs($number);
        });
        // Register the ceiling function
        $this->registerTemplateFunction("ceil", function ($number)
        {
            return ceil($number);
        });
        // Register the charset function
        $this->registerTemplateFunction("charset", function ($charset)
        {
            return '<meta charset="' . $charset . '">';
        });
        // Register the CSS function
        $this->registerTemplateFunction("css", function ($paths)
        {
            if(!is_array($paths))
            {
                $paths = [$paths];
            }

            $callback = function($path)
            {
                return '<link href="' . $path . '" rel="stylesheet">';
            };

            return implode("\n", array_map($callback, $paths));
        });
        // Register the count function
        $this->registerTemplateFunction("count", function (array $array)
        {
            return count($array);
        });
        // Register the date function
        $this->registerTemplateFunction("date", function ($format, $timestamp = null)
        {
            if($timestamp === null)
            {
                $timestamp = time();
            }

            return date($format, $timestamp);
        });
        // Register the favicon function
        $this->registerTemplateFunction("favicon", function ($path)
        {
            return '<link href="' . $path . '" rel="shortcut icon">';
        });
        // Register the floor function
        $this->registerTemplateFunction("floor", function ($number)
        {
            return floor($number);
        });
        // Register the format DateTime function
        $this->registerTemplateFunction('formatDateTime', function (\DateTime $date, $format = "m/d/Y", $timeZone = null)
        {
            if(is_string($timeZone) && in_array($timeZone, \DateTimeZone::listIdentifiers()))
            {
                $timeZone = new \DateTimeZone($timeZone);
            }

            if($timeZone instanceof \DateTimeZone)
            {
                $date->setTimezone($timeZone);
            }

            return $date->format($format);
        });
        // Register the HTTP-equiv function
        $this->registerTemplateFunction("httpEquiv", function ($name, $value)
        {
            return '<meta http-equiv="' . htmlentities($name) . '" content="' . htmlentities($value) . '">';
        });
        // Register the implode function
        $this->registerTemplateFunction("implode", function ($glue, array $pieces)
        {
            return implode($glue, $pieces);
        });
        // Register the JSON encode function
        $this->registerTemplateFunction("json_encode", function ($value, $options = 0, $depth = 512)
        {
            return json_encode($value, $options, $depth);
        });
        // Register the lowercase first function
        $this->registerTemplateFunction("lcfirst", function ($string)
        {
            return lcfirst($string);
        });
        // Register the meta description function
        $this->registerTemplateFunction("metaDescription", function ($metaDescription)
        {
            return '<meta name="description" content="' . htmlentities($metaDescription) . '">';
        });
        // Register the meta keywords function
        $this->registerTemplateFunction("metaKeywords", function (array $metaKeywords)
        {
            return '<meta name="keywords" content="' . implode(",", array_map("htmlentities", $metaKeywords)) . '">';
        });
        // Register the page title function
        $this->registerTemplateFunction("pageTitle", function ($title)
        {
            return '<title>' . htmlentities($title) . '</title>';
        });
        // Register the round function
        $this->registerTemplateFunction("round", function ($number, $precision = 0, $mode = PHP_ROUND_HALF_UP)
        {
            return round($number, $precision, $mode);
        });
        // Register the script function
        $this->registerTemplateFunction("script", function ($paths, $type = "text/javascript")
        {
            if(!is_array($paths))
            {
                $paths = [$paths];
            }

            $callback = function($path) use ($type)
            {
                return '<script type="' . $type . '" src="' . $path . '"></script>';
            };

            return implode("\n", array_map($callback, $paths));
        });
        // Register the lowercase function
        $this->registerTemplateFunction("strtolower", function ($string)
        {
            return strtolower($string);
        });
        // Register the lowercase function
        $this->registerTemplateFunction("strtoupper", function ($string)
        {
            return strtoupper($string);
        });
        // Register the substring function
        $this->registerTemplateFunction("substr", function ($string, $start, $length = null)
        {
            if($length === null)
            {
                return substr($string, $start);
            }

            return substr($string, $start, $length);
        });
        // Register the trim function
        $this->registerTemplateFunction("trim", function ($string, $characterMask = " \t\n\r\0\x0B")
        {
            return trim($string, $characterMask);
        });
        // Register the uppercase first function
        $this->registerTemplateFunction("ucfirst", function ($string)
        {
            return ucfirst($string);
        });
        // Register the uppercase words function
        $this->registerTemplateFunction("ucwords", function ($string)
        {
            return ucwords($string);
        });
        // Register the URL decode function
        $this->registerTemplateFunction("urldecode", function ($string)
        {
            return urldecode($string);
        });
        // Register the URL encode function
        $this->registerTemplateFunction("urlencode", function ($string)
        {
            return urlencode($string);
        });
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
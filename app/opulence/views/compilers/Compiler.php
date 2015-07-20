<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines methods for compiling templates
 */
namespace Opulence\Views\Compilers;
use InvalidArgumentException;
use Opulence\Views\Caching\ICache;
use Opulence\Views\Compilers\SubCompilers\Fortune\FortuneTemplateFunctionRegistrant;
use Opulence\Views\Compilers\SubCompilers\ISubCompiler;
use Opulence\Views\Compilers\SubCompilers\StatementCompiler;
use Opulence\Views\Compilers\SubCompilers\TagCompiler;
use Opulence\Views\Factories\ITemplateFactory;
use Opulence\Views\Filters\IFilter;
use Opulence\Views\ITemplate;

class Compiler implements ICompiler
{
    /** @var array The list of custom compilers */
    protected $subCompilers = [
        "preCache" => [],
        "priority" => [],
        "nonPriority" => []
    ];
    /** @var ICache The cache to use for compiled templates */
    protected $cache = null;
    /** @var array The mapping of function names to their callbacks */
    protected $templateFunctions = [];

    /**
     * @param ICache $cache The cache to use for compiled templates
     * @param ITemplateFactory $templateFactory The factory that creates templates
     * @param IFilter $xssFilter The cross-site scripting filter
     */
    public function __construct(ICache $cache, ITemplateFactory $templateFactory, IFilter $xssFilter)
    {
        $this->cache = $cache;
        // Order here matters
        $this->registerSubCompiler(new StatementCompiler($this, $templateFactory), null, true);
        $this->registerSubCompiler(new TagCompiler($this, $xssFilter));
        $templateFunctionRegistrant = new FortuneTemplateFunctionRegistrant();
        $templateFunctionRegistrant->registerTemplateFunctions($this);
    }

    /**
     * {@inheritdoc}
     */
    public function callTemplateFunction($functionName, array $args = [])
    {
        if(!isset($this->templateFunctions[$functionName]))
        {
            throw new InvalidArgumentException("Template function \"$functionName\" does not exist");
        }

        return call_user_func_array($this->templateFunctions[$functionName], $args);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(ITemplate $template)
    {
        $template->prepare();
        $preCacheContents = $this->runPreCacheSubCompilers($template, $template->getContents());
        $compiledContents = $this->cache->get($preCacheContents, $template->getVars(), $template->getTags());

        if($compiledContents === null)
        {
            $compiledContents = $this->runPrioritySubCompilers($template, $preCacheContents);
            $compiledContents = $this->runNonPrioritySubCompilers($template, $compiledContents);

            // Remember this for next time
            $this->cache->set(
                $compiledContents,
                $preCacheContents,
                $template->getVars(),
                $template->getTags()
            );
        }

        return $compiledContents;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateFunctions()
    {
        return $this->templateFunctions;
    }

    /**
     * {@inheritdoc}
     */
    public function registerSubCompiler(SubCompilers\ISubCompiler $subCompiler, $priority = null, $isPreCache = false)
    {
        if($isPreCache)
        {
            $this->subCompilers["preCache"][] = $subCompiler;
        }
        elseif($priority === null)
        {
            $this->subCompilers["nonPriority"][] = $subCompiler;
        }
        elseif(!is_int($priority) || $priority < 1)
        {
            throw new InvalidArgumentException("Priority must be positive integer");
        }
        else
        {
            $this->subCompilers["priority"][] = [
                "subCompiler" => $subCompiler,
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
     * Runs the non-priority sub-compilers
     *
     * @param ITemplate $template The view to compile
     * @param string $content The uncompiled contents
     * @return string The compiled contents
     */
    private function runNonPrioritySubCompilers(ITemplate $template, $content)
    {
        /** @var ISubCompiler $subCompiler */
        foreach($this->subCompilers["nonPriority"] as $subCompiler)
        {
            $content = $subCompiler->compile($template, $content);
        }

        return $content;
    }

    /**
     * Runs the pre-cache sub-compilers
     *
     * @param ITemplate $template The view to compile
     * @param string $content The uncompiled contents
     * @return string The compiled contents
     */
    private function runPreCacheSubCompilers(ITemplate $template, $content)
    {
        /** @var ISubCompiler $subCompiler */
        foreach($this->subCompilers["preCache"] as $subCompiler)
        {
            $content = $subCompiler->compile($template, $content);
        }

        return $content;
    }

    /**
     * Runs the priority sub-compilers
     *
     * @param ITemplate $template The view to compile
     * @param string $content The uncompiled contents
     * @return string The compiled contents
     */
    private function runPrioritySubCompilers(ITemplate $template, $content)
    {
        // Sort the sub-compilers by their priorities
        usort($this->subCompilers["priority"], [$this, "sortPriority"]);

        // Compile the priority sub-compilers
        foreach($this->subCompilers["priority"] as $subCompilerData)
        {
            /** @var ISubCompiler $subCompiler */
            $subCompiler = $subCompilerData["subCompiler"];
            $content = $subCompiler->compile($template, $content);
        }

        return $content;
    }

    /**
     * Sorts two arrays of sub-compilers by comparing their priorities
     *
     * @param array $a An array containing the priority and sub-compiler
     * @param array $b An array containing the priority and sub-compiler
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
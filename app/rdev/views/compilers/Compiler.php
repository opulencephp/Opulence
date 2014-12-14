<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines methods for compiling templates
 */
namespace RDev\Views\Compilers;
use RDev\Views;
use RDev\Views\Cache;
use RDev\Views\Factories;
use RDev\Views\Filters;

class Compiler implements ICompiler
{
    /** @var array The list of custom compilers */
    protected $subCompilers = [
        "preCache" => [],
        "priority" => [],
        "nonPriority" => []
    ];
    /** @var Cache\ICache The cache to use for compiled templates */
    protected $cache = null;
    /** @var array The mapping of function names to their callbacks */
    protected $templateFunctions = [];

    /**
     * @param Cache\ICache $cache The cache to use for compiled templates
     * @param Factories\ITemplateFactory $templateFactory The factory that creates templates
     * @param Filters\IFilter $xssFilter The cross-site scripting filter
     */
    public function __construct(
        Cache\ICache $cache,
        Factories\ITemplateFactory $templateFactory,
        Filters\IFilter $xssFilter
    )
    {
        $this->cache = $cache;

        // Order here matters
        $this->registerSubCompiler(new SubCompilers\StatementCompiler($this, $templateFactory), null, true);
        $this->registerSubCompiler(new SubCompilers\PHPCompiler($this, $xssFilter));
        $this->registerSubCompiler(new SubCompilers\TagCompiler($this, $xssFilter));
        $templateFunctionRegistrant = new BuiltInTemplateFunctionRegistrant();
        $templateFunctionRegistrant->registerTemplateFunctions($this);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(Views\ITemplate $template)
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
            throw new \InvalidArgumentException("Priority must be positive integer");
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
     * @param Views\ITemplate $template The view to compile
     * @param string $content The uncompiled contents
     * @return string The compiled contents
     */
    private function runNonPrioritySubCompilers(Views\ITemplate $template, $content)
    {
        /** @var SubCompilers\ISubCompiler $subCompiler */
        foreach($this->subCompilers["nonPriority"] as $subCompiler)
        {
            $content = $subCompiler->compile($template, $content);
        }

        return $content;
    }

    /**
     * Runs the pre-cache sub-compilers
     *
     * @param Views\ITemplate $template The view to compile
     * @param string $content The uncompiled contents
     * @return string The compiled contents
     */
    private function runPreCacheSubCompilers(Views\ITemplate $template, $content)
    {
        /** @var SubCompilers\ISubCompiler $subCompiler */
        foreach($this->subCompilers["preCache"] as $subCompiler)
        {
            $content = $subCompiler->compile($template, $content);
        }

        return $content;
    }

    /**
     * Runs the priority sub-compilers
     *
     * @param Views\ITemplate $template The view to compile
     * @param string $content The uncompiled contents
     * @return string The compiled contents
     */
    private function runPrioritySubCompilers(Views\ITemplate $template, $content)
    {
        // Sort the sub-compilers by their priorities
        usort($this->subCompilers["priority"], [$this, "sortPriority"]);

        // Compile the priority sub-compilers
        foreach($this->subCompilers["priority"] as $subCompilerData)
        {
            /** @var SubCompilers\ISubCompiler $subCompiler */
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
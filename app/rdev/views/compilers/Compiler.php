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
     * @param Factories\ITemplateFactory $templateFactory The factory that creates templates
     * @param Filters\IFilter $xssFilter The cross-site scripting filter
     */
    public function __construct(
        Cache\ICache $cache,
        Factories\ITemplateFactory $templateFactory,
        Filters\IFilter $xssFilter
    )
    {
        $this->setCache($cache);
        $this->setXSSFilter($xssFilter);

        // Order here matters
        $this->registerSubCompiler(new SubCompilers\Statement($this, $templateFactory));
        $this->registerSubCompiler(new SubCompilers\PHP($this));
        $this->registerSubCompiler(new SubCompilers\Tag($this));
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
            usort($this->subCompilers["priority"], [$this, "sortPriority"]);

            $compiledContents = $uncompiledContents;

            // Compile the priority compilers
            foreach($this->subCompilers["priority"] as $compileFunctionData)
            {
                /** @var SubCompilers\ISubCompiler $subCompiler */
                $subCompiler = $compileFunctionData["subCompiler"];
                $compiledContents = $subCompiler->compile($template, $compiledContents);
            }

            // Compile the non-priority compilers
            foreach($this->subCompilers["nonPriority"] as $subCompiler)
            {
                $compiledContents = $subCompiler->compile($template, $compiledContents);
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
    public function getTemplateFunctions()
    {
        return $this->templateFunctions;
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
    public function registerSubCompiler(SubCompilers\ISubCompiler $subCompiler, $priority = null)
    {
        if($priority === null)
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
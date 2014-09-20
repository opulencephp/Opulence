<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines methods for compiling templates
 */
namespace RDev\Views\Templates;
use RDev\Views\Security;

class Compiler implements ICompiler
{
    /** @var array The list of custom compile functions */
    protected $compileFunctions = [
        "priority" => [],
        "nonPriority" => []
    ];

    public function __construct()
    {
        // Don't do anything
    }

    /**
     * {@inheritdoc}
     */
    public function compile($template)
    {
        // Sort the compile functions by their priorities
        usort($this->compileFunctions["priority"], [$this, "sortPriority"]);

        // Compile the non-priority compilers
        foreach($this->compileFunctions["priority"] as $compileFunctionData)
        {
            $template = call_user_func_array($compileFunctionData["compiler"], [$template]);
        }

        // Compile the non-priority compilers
        foreach($this->compileFunctions["nonPriority"] as $compileFunction)
        {
            $template = call_user_func_array($compileFunction, [$template]);
        }

        return $template;
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
            $this->compileFunctions["nonPriority"][] = $compiler;
        }
        elseif(!is_int($priority) || $priority < 1)
        {
            throw new \InvalidArgumentException("Priority must be positive integer");
        }
        else
        {
            $this->compileFunctions["priority"][] = [
                "compiler" => $compiler,
                "priority" => $priority
            ];
        }
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
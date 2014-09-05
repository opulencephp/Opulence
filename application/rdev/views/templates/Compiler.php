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
        foreach($this->compileFunctions as $priorityKey => $compileFunctionsByPriority)
        {
            foreach($compileFunctionsByPriority as $compileFunction)
            {
                $template = call_user_func_array($compileFunction, [$template]);
            }
        }

        return $template;
    }

    /**
     * {@inheritdoc}
     */
    public function registerCompiler($compiler, $hasPriority = false)
    {
        if(!is_callable($compiler, true))
        {
            throw new \InvalidArgumentException("Compiler is not callable");
        }

        if($hasPriority)
        {
            $this->compileFunctions["priority"][] = $compiler;
        }
        else
        {
            $this->compileFunctions["nonPriority"][] = $compiler;
        }
    }
} 
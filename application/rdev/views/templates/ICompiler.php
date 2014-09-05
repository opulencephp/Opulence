<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for template compilers to implement
 */
namespace RDev\Views\Templates;

interface ICompiler
{
    /**
     * Gets the compiled template
     *
     * @param string $template The template to render
     * @return string The compiled template
     * @throws \RuntimeException Thrown if there was an error compiling the template
     */
    public function compile($template);

    /**
     * Registers a custom compiler
     *
     * @param callable|array $compiler The anonymous function to execute to compile custom functions inside tags
     *      The function must take in one parameter: the template contents
     *      The function must return the compile template's contents
     *      Alternatively, it can be an array that is_callable()
     * @param bool $hasPriority True if the compiler should have higher precedence
     *      If true, the compiler will be executed in the order it was added to the list of prioirty compilers
     *      If false, the compiler will be executed in the order it was added to the list of non-priority compilers
     * @throw new \InvalidArgumentException Thrown if the compiler is not callable
     */
    public function registerCompiler($compiler, $hasPriority = false);
} 
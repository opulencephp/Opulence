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
     * @param int|bool $priority The priority (1 is the highest) in which the compiler is run
     *      If no priority is given, the compiler will be executed in the order it was registered
     * @throw new \InvalidArgumentException Thrown if the compiler is not callable or if the priority is invalid
     */
    public function registerCompiler($compiler, $priority = null);
} 
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
     * @param ITemplate $template The template to render
     * @return string The compiled template
     * @throws \RuntimeException Thrown if there was an error compiling the template
     */
    public function compile(ITemplate $template);

    /**
     * Registers a custom compiler
     *
     * @param callable|array $compiler The anonymous function to execute to compile custom functions inside tags
     *      The function must take in two parameter: the template object and the contents to compile
     *      The function must return the compiled template's contents
     *      Alternatively, it can be an array that is_callable()
     * @param int|null $priority The priority (1 is the highest) in which the compiler is run
     *      If no priority is given, the compiler will be executed in the order it was registered
     * @throw new \InvalidArgumentException Thrown if the compiler is not callable or if the priority is invalid
     */
    public function registerCompiler($compiler, $priority = null);

    /**
     * Registers a function that appears in a template
     * Useful for defining functions for consistent formatting in a template
     *
     * @param string $functionName The name of the function as it'll appear in the template
     * @param callable $function The function that returns the replacement string for the function in a template
     *      It must accept one parameter (the template's contents) and return a printable value
     */
    public function registerTemplateFunction($functionName, callable $function);

    /**
     * Sets the cache to use
     *
     * @param ICache $cache The cache to use
     */
    public function setCache(ICache $cache);
} 
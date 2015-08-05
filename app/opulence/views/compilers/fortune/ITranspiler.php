<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for Fortune transpilers to implement
 */
namespace Opulence\Views\Compilers\Fortune;
use InvalidArgumentException;
use Opulence\Views\IView;

interface ITranspiler
{
    /**
     * Appends text to the end of the transpiled contents
     *
     * @param string $text The text to append
     */
    public function append($text);

    /**
     * Calls a view function
     * Pass in any arguments as the 2nd, 3rd, 4th, etc parameters
     *
     * @param string $functionName The name of the function to call
     * @return mixed The output of the view function
     * @throws InvalidArgumentException Thrown if the function name is invalid
     */
    public function callViewFunction($functionName);

    /**
     * Ends a view part
     */
    public function endPart();

    /**
     * Registers a directive transpiler
     *
     * @param string $name The name of the directive whose transpiler we're registering
     * @param callable $transpiler The transpiler, which accepts an optional expression from the directive
     */
    public function registerDirectiveTranspiler($name, callable $transpiler);

    /**
     * Registers a function that appears in a view
     * Useful for defining functions for consistent formatting in a view
     *
     * @param string $functionName The name of the function as it'll appear in the view
     * @param callable $function The function that returns the replacement string for the function in a view
     *      It must accept one parameter (the view's contents) and return a printable value
     */
    public function registerViewFunction($functionName, callable $function);

    /**
     * Sanitizes a value
     *
     * @param mixed $value The value to sanitize
     * @return string The sanitized value
     */
    public function sanitize($value);

    /**
     * Shows a view part
     *
     * @param string $name The name of the part to show
     * @return string The content of the part
     */
    public function showPart($name);

    /**
     * Starts a view part
     *
     * @param string $name The name of the part to start
     */
    public function startPart($name);

    /**
     * @inheritdoc
     */
    public function transpile(IView $view, $contents = null);
}
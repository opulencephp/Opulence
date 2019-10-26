<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Views\Compilers\Fortune;

use InvalidArgumentException;
use Opulence\Views\IView;

/**
 * Defines the interface for Fortune transpilers to implement
 */
interface ITranspiler
{
    /**
     * Adds a parent to the current view
     *
     * @param IView $parent The parent to add
     * @param IView $child The child to add parents to
     */
    public function addParent(IView $parent, IView $child);

    /**
     * Appends text to the end of the transpiled contents
     *
     * @param string $text The text to append
     */
    public function append(string $text);

    /**
     * Calls a view function
     * Pass in any arguments as the 2nd, 3rd, 4th, etc parameters
     *
     * @param string $functionName The name of the function to call
     * @param mixed ...$args The list of args
     * @return mixed The output of the view function
     * @throws InvalidArgumentException Thrown if the function name is invalid
     */
    public function callViewFunction(string $functionName, ...$args);

    /**
     * Ends a view part
     */
    public function endPart();

    /**
     * Prepends text to the beginning of the transpiled contents
     *
     * @param string $text The text to prepend
     */
    public function prepend(string $text);

    /**
     * Registers a directive transpiler
     *
     * @param string $name The name of the directive whose transpiler we're registering
     * @param callable $transpiler The transpiler, which accepts a noncompulsory expression from the directive
     */
    public function registerDirectiveTranspiler(string $name, callable $transpiler);

    /**
     * Registers a function that appears in a view
     * Useful for defining functions for consistent formatting in a view
     *
     * @param string $functionName The name of the function as it'll appear in the view
     * @param callable $function The function that returns the replacement string for the function in a view
     *      It must accept one parameter (the view's contents) and return a printable value
     */
    public function registerViewFunction(string $functionName, callable $function);

    /**
     * Sanitizes a value
     *
     * @param mixed $value The value to sanitize
     * @return string The sanitized value
     */
    public function sanitize($value) : string;

    /**
     * Shows a view part
     *
     * @param string $name The name of the part to show, or empty if we should show the last part in the stack
     * @return string The content of the part
     */
    public function showPart(string $name = '') : string;

    /**
     * Starts a view part
     *
     * @param string $name The name of the part to start
     */
    public function startPart(string $name);

    /**
     * Transpiles a view to PHP code
     *
     * @param IView $view The view to compile
     * @return string The transpiled PHP code
     */
    public function transpile(IView $view) : string;
}

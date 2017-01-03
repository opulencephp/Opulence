<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Views\Factories;

use InvalidArgumentException;
use Opulence\Views\IView;

/**
 * Defines the interface for view factories to implement
 */
interface IViewFactory
{
    /**
     * Creates a view from the file at the input path
     * If any builders are registered for this view, they're run too
     *
     * @param string $name The path relative to the root view directory
     * @return IView The view with the contents from the path
     * @throws InvalidArgumentException Thrown if the view does not exist
     */
    public function createView(string $name) : IView;

    /**
     * Checks whether or not a view exists
     *
     * @param string $name The name of the view to search for
     * @return bool True if the view exists, otherwise false
     */
    public function hasView(string $name) : bool;

    /**
     * Registers a builder for a particular view
     * Every time this view is created, the builders are run on it
     * Builders are run in the order they're registered
     *
     * @param string|array $names The alias(es) or path(s) of the view relative to the root view directory
     * @param callable $callback The callback that will return the built view
     */
    public function registerBuilder($names, callable $callback);
}
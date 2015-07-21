<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for view factories to implement
 */
namespace Opulence\Views\Factories;
use Opulence\Files\FileSystemException;
use Opulence\Views\IView;

interface IViewFactory
{
    /**
     * Aliases a view path
     * Useful for registering builders for multiple views that use the same view
     *
     * @param string $alias The alias to use
     * @param string $viewPath The path relative to the root view directory
     */
    public function alias($alias, $viewPath);

    /**
     * Creates a view from the file at the input path
     * If any builders are registered for this view, they're run too
     *
     * @param string $name The alias or path relative to the root view directory
     * @return IView The view with the contents from the path
     * @throws FileSystemException Thrown if the view does not exist
     */
    public function create($name);

    /**
     * Registers a builder for a particular view
     * Every time this view is created, the builders are run on it
     * Builders are run in the order they're registered
     *
     * @param string|array $names The alias(es) or path(s) of the view relative to the root view directory
     * @param callable $callback The callback that will return an instance of a builder
     */
    public function registerBuilder($names, callable $callback);
}
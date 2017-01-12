<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Views\Factories;

use InvalidArgumentException;
use Opulence\Views\Factories\IO\IViewNameResolver;
use Opulence\Views\Factories\IO\IViewReader;
use Opulence\Views\IView;
use Opulence\Views\View;

/**
 * Defines the view factory
 */
class ViewFactory implements IViewFactory
{
    /** @var IViewNameResolver The view name resolver used to get paths to views */
    protected $viewNameResolver = null;
    /** @var IViewReader The view reader */
    protected $viewReader = null;
    /** @var array The mapping of view paths to a list of builders to run whenever the view is created */
    protected $builders = [];

    /**
     * @param IViewNameResolver $viewNameResolver The view name resolver used to get paths to views
     * @param IViewReader $viewReader The view reader
     */
    public function __construct(IViewNameResolver $viewNameResolver, IViewReader $viewReader)
    {
        $this->viewNameResolver = $viewNameResolver;
        $this->viewReader = $viewReader;
    }

    /**
     * @inheritdoc
     */
    public function createView(string $name) : IView
    {
        $resolvedPath = $this->viewNameResolver->resolve($name);
        $content = $this->viewReader->read($resolvedPath);
        $view = new View($resolvedPath, $content);

        return $this->runBuilders($name, $resolvedPath, $view);
    }

    /**
     * @inheritdoc
     */
    public function hasView(string $name) : bool
    {
        try {
            $this->viewNameResolver->resolve($name);

            return true;
        } catch (InvalidArgumentException $ex) {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function registerBuilder($names, callable $callback)
    {
        foreach ((array)$names as $name) {
            if (!isset($this->builders[$name])) {
                $this->builders[$name] = [];
            }

            $this->builders[$name][] = $callback;
        }
    }

    /**
     * Runs the builders for a view (if there any)
     *
     * @param string $name The name of the view file
     * @param string $resolvedPath The resolved path to the view file
     * @param IView $view The view to run builders on
     * @return IView The built view
     */
    protected function runBuilders(string $name, string $resolvedPath, IView $view) : IView
    {
        $builders = [];

        // If there's a builder registered to the same name as the view
        if (isset($this->builders[$name])) {
            $builders = $this->builders[$name];
        } else {
            $pathInfo = pathinfo($resolvedPath);
            $filename = $pathInfo['filename'];
            $basename = $pathInfo['basename'];

            /**
             * If there's a builder registered without the extension and it resolves to the correct view file path
             * Else if there's a builder registered with the extension and it resolves to the correct view file path
             */
            if (isset($this->builders[$filename]) && $this->viewNameResolver->resolve($filename) == $resolvedPath) {
                $builders = $this->builders[$filename];
            } elseif (isset($this->builders[$basename]) && $this->viewNameResolver->resolve($basename) == $resolvedPath) {
                $builders = $this->builders[$basename];
            }
        }

        foreach ($builders as $callback) {
            $view = $callback($view);
        }

        return $view;
    }
}

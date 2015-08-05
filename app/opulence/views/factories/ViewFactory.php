<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the view factory
 */
namespace Opulence\Views\Factories;
use Opulence\Files\FileSystem;
use Opulence\Views\IBuilder;
use Opulence\Views\IView;
use Opulence\Views\View;

class ViewFactory implements IViewFactory
{
    /** @var IViewNameResolver The view name resolver used to get paths to views */
    protected $viewNameResolver = null;
    /** @var FileSystem The file system to read views with */
    protected $fileSystem = null;
    /** @var array The mapping of view paths to a list of builders to run whenever the view is created */
    protected $builders = [];

    /**
     * @param IViewNameResolver $viewNameResolver The view name resolver used to get paths to views
     * @param FileSystem $fileSystem The file system to read views with
     */
    public function __construct(IViewNameResolver $viewNameResolver, FileSystem $fileSystem)
    {
        $this->viewNameResolver = $viewNameResolver;
        $this->fileSystem = $fileSystem;
    }

    /**
     * @inheritdoc
     */
    public function create($name)
    {
        $resolvedPath = $this->viewNameResolver->resolve($name);
        $content = $this->fileSystem->read($resolvedPath);
        $view = new View($resolvedPath, $content);

        return $this->runBuilders($name, $view);
    }

    /**
     * @inheritdoc
     */
    public function registerBuilder($names, callable $callback)
    {
        foreach((array)$names as $name)
        {
            if(!isset($this->builders[$name]))
            {
                $this->builders[$name] = [];
            }

            $this->builders[$name][] = $callback;
        }
    }

    /**
     * Runs the builders for a view (if there any)
     *
     * @param string $name The name of the view file
     * @param IView $view The view to run builders on
     * @return IView The built view
     */
    protected function runBuilders($name, IView $view)
    {
        if(isset($this->builders[$name]))
        {
            foreach($this->builders[$name] as $callback)
            {
                /** @var IBuilder $builder */
                $builder = $callback();
                $view = $builder->build($view);
            }
        }

        return $view;
    }
}
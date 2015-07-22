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

abstract class ViewFactory implements IViewFactory
{
    /** @var FileSystem The file system to read views with */
    protected $fileSystem = null;
    /** @var string The root directory of the views */
    protected $rootViewDirectory = "";
    /** @var array The mapping of view paths to a list of builders to run whenever the view is created */
    protected $builders = [];

    /**
     * @param FileSystem $fileSystem The file system to read views with
     * @param string|null $rootViewDirectory The root directory of the views if it's known, otherwise null
     */
    public function __construct(FileSystem $fileSystem, $rootViewDirectory = null)
    {
        $this->fileSystem = $fileSystem;

        if($rootViewDirectory !== null)
        {
            $this->setRootViewDirectory($rootViewDirectory);
        }
    }

    /**
     * @inheritdoc
     */
    public function create($name)
    {
        $name = ltrim($name, "/");
        $path = "{$this->rootViewDirectory}/$name.{$this->getExtension()}";
        $content = $this->fileSystem->read($path);
        $view = $this->createView($path, $content);

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
     * @param string $rootViewDirectory
     */
    public function setRootViewDirectory($rootViewDirectory)
    {
        $this->rootViewDirectory = rtrim($rootViewDirectory, "/");
    }

    /**
     * Creates a view from the path and contents of a raw view
     *
     * @param string $path The path to the raw view
     * @param string $content The contents of the view
     * @return IView The view
     */
    abstract protected function createView($path, $content);

    /**
     * Gets the extension of view files created by this factory
     *
     * @return string The extension
     */
    abstract protected function getExtension();

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
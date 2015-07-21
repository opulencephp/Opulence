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
    /** @var FileSystem The file system to read views with */
    private $fileSystem = null;
    /** @var string The root directory of the views */
    private $rootViewDirectory = "";
    /** @var array The mapping of view paths to a list of builders to run whenever the view is created */
    private $builders = [];
    /** @var array The mapping of aliases to their view paths */
    private $aliases = [];

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
     * {@inheritdoc}
     */
    public function alias($alias, $viewPath)
    {
        $this->aliases[$alias] = $viewPath;
    }

    /**
     * {@inheritdoc}
     */
    public function create($name)
    {
        $isAlias = $this->isAlias($name);
        $viewPath = $name;

        if($isAlias)
        {
            $viewPath = $this->aliases[$name];
        }

        $viewPath = ltrim($viewPath, "/");
        $content = $this->fileSystem->read($this->rootViewDirectory . "/" . $viewPath);
        $view = new FortuneView($content);
        $view = $this->runBuilders($viewPath, $view);

        if($isAlias)
        {
            // Run any builders registered to the alias
            $view = $this->runBuilders($name, $view);
        }

        return $view;
    }

    /**
     * {@inheritdoc}
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
     * Gets whether or not something is an alias to a view path
     *
     * @param string $name The item to check
     * @return bool True if the input is an alias, otherwise false
     */
    private function isAlias($name)
    {
        return isset($this->aliases[$name]);
    }

    /**
     * Runs the builders for a view (if there any)
     *
     * @param string $viewPath The path of the view relative to the root view directory
     * @param IView $view The view to run builders on
     * @return IView The built view
     */
    private function runBuilders($viewPath, IView $view)
    {
        if(isset($this->builders[$viewPath]))
        {
            foreach($this->builders[$viewPath] as $callback)
            {
                /** @var IBuilder $builder */
                $builder = $callback();
                $view = $builder->build($view);
            }
        }

        return $view;
    }
}
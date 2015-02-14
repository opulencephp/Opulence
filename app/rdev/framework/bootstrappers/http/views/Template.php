<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the template bootstrapper
 */
namespace RDev\Framework\Bootstrappers\HTTP\Views;
use RDev\Applications\Environments;
use RDev\Applications\Bootstrappers;
use RDev\Framework;
use RDev\IoC;
use RDev\Views\Cache;
use RDev\Views\Compilers;
use RDev\Views\Factories;
use RDev\Views\Filters;

class Template extends Bootstrappers\Bootstrapper
{
    /** @var Cache\ICache The view cache */
    protected $viewCache = null;
    /** @var Factories\ITemplateFactory The template factory */
    protected $templateFactory = null;

    /**
     * {@inheritdoc}
     */
    public function registerBindings(IoC\IContainer $container)
    {
        $this->viewCache = $this->getViewCache($container);
        $this->templateFactory = $this->getTemplateFactory($container);
        $compiler = $this->getViewCompiler($container);
        $container->bind("RDev\\Views\\Cache\\ICache", $this->viewCache);
        $container->bind("RDev\\Views\\Compilers\\ICompiler", $compiler);
        $container->bind("RDev\\Views\\Factories\\ITemplateFactory", $this->templateFactory);
    }

    /**
     * Finishes setting necessary properties for template components
     */
    public function run()
    {
        // If we're developing, wipe out the view cache
        if($this->environment->getName() == Environments\Environment::DEVELOPMENT)
        {
            $this->viewCache->flush();
        }
    }

    /**
     * Gets the view template factory
     * To use a different template factory than the one returned here, extend this class and override this method
     *
     * @param IoC\IContainer $container The dependency injection container
     * @return Factories\ITemplateFactory The template factory
     */
    protected function getTemplateFactory(IoC\IContainer $container)
    {
        $fileSystem = $container->makeShared("RDev\\Files\\FileSystem");

        return new Factories\TemplateFactory($fileSystem, $this->paths["views"]);
    }

    /**
     * Gets the view cache
     * To use a different view cache than the one returned here, extend this class and override this method
     *
     * @param IoC\IContainer $container The dependency injection container
     * @return Cache\ICache The view cache
     */
    protected function getViewCache(IoC\IContainer $container)
    {
        $fileSystem = $container->makeShared("RDev\\Files\\FileSystem");
        $cacheConfig = require_once $this->paths["configs"] . "/http/views.php";

        return new Cache\Cache(
            $fileSystem,
            $this->paths["compiledViews"],
            $cacheConfig["cacheLifetime"],
            $cacheConfig["gcChance"],
            $cacheConfig["gcTotal"]
        );
    }

    /**
     * Gets the view compiler
     * To use a different view compiler than the one returned here, extend this class and override this method
     *
     * @param IoC\IContainer $container The dependency injection container
     * @return Compilers\ICompiler The view compiler
     */
    protected function getViewCompiler(IoC\IContainer $container)
    {
        return new Compilers\Compiler($this->viewCache, $this->templateFactory, new Filters\XSS());
    }
}
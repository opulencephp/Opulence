<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the template bootstrapper
 */
namespace RDev\Framework\Bootstrappers\HTTP\Views;
use RDev\Applications\Environments\Environment;
use RDev\Applications\Bootstrappers;
use RDev\IoC\IContainer;
use RDev\Views\Caching\Cache;
use RDev\Views\Caching\ICache;
use RDev\Views\Compilers\Compiler;
use RDev\Views\Compilers\ICompiler;
use RDev\Views\Factories\TemplateFactory;
use RDev\Views\Factories\ITemplateFactory;
use RDev\Views\Filters\XSSFilter;

class Template extends Bootstrappers\Bootstrapper
{
    /** @var ICache The view cache */
    protected $viewCache = null;
    /** @var ITemplateFactory The template factory */
    protected $templateFactory = null;

    /**
     * {@inheritdoc}
     */
    public function registerBindings(IContainer $container)
    {
        $this->viewCache = $this->getViewCache($container);
        $this->templateFactory = $this->getTemplateFactory($container);
        $compiler = $this->getViewCompiler($container);
        $container->bind("RDev\\Views\\Caching\\ICache", $this->viewCache);
        $container->bind("RDev\\Views\\Compilers\\ICompiler", $compiler);
        $container->bind("RDev\\Views\\Factories\\ITemplateFactory", $this->templateFactory);
    }

    /**
     * Finishes setting necessary properties for template components
     */
    public function run()
    {
        // If we're developing, wipe out the view cache
        if($this->environment->getName() == Environment::DEVELOPMENT)
        {
            $this->viewCache->flush();
        }
    }

    /**
     * Gets the view template factory
     * To use a different template factory than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     * @return ITemplateFactory The template factory
     */
    protected function getTemplateFactory(IContainer $container)
    {
        $fileSystem = $container->makeShared("RDev\\Files\\FileSystem");

        return new TemplateFactory($fileSystem, $this->paths["views"]);
    }

    /**
     * Gets the view cache
     * To use a different view cache than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     * @return ICache The view cache
     */
    protected function getViewCache(IContainer $container)
    {
        $fileSystem = $container->makeShared("RDev\\Files\\FileSystem");
        $cacheConfig = require_once $this->paths["configs"] . "/http/views.php";

        return new Cache(
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
     * @param IContainer $container The dependency injection container
     * @return ICompiler The view compiler
     */
    protected function getViewCompiler(IContainer $container)
    {
        return new Compiler($this->viewCache, $this->templateFactory, new XSSFilter());
    }
}
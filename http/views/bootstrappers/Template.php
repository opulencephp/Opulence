<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the template bootstrapper
 */
namespace RDev\Framework\HTTP\Views\Bootstrappers;
use RDev\Applications\Bootstrappers;
use RDev\Applications\Environments;
use RDev\Framework;
use RDev\IoC;
use RDev\Views\Cache;
use RDev\Views\Compilers;
use RDev\Views\Filters;

class Template implements Bootstrappers\IBootstrapper
{
    /** @var IoC\IContainer The dependency injection container to use */
    private $container = null;
    /** @var Environments\Environment The application environment */
    private $environment = null;
    /** @var Framework\Paths The application paths */
    private $paths = null;

    /**
     * @param IoC\IContainer $container The dependency injection container to use
     * @param Environments\Environment $environment The application environment
     * @param Framework\Paths $paths The application paths
     */
    public function __construct(IoC\IContainer $container, Environments\Environment $environment, Framework\Paths $paths)
    {
        $this->container = $container;
        $this->environment = $environment;
        $this->paths = $paths;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        /** @var Cache\Cache $cache */
        $cache = $this->container->makeShared("RDev\\Views\\Cache\\Cache", [
            // The path to store compiled templates
            // Make sure this path is writable
            $this->paths["compiledViews"],
            // The lifetime of cached templates
            3600,
            // The chance that garbage collection will be run
            1,
            // The number the chance will be divided by to calculate the probability (default is 1 in 100 chance)
            100
        ]);
        $templateFactory = $this->container->makeShared("RDev\\Views\\Factories\\TemplateFactory", [
            // The path to the template directory
            $this->paths["views"]
        ]);
        $compiler = new Compilers\Compiler($cache, $templateFactory, new Filters\XSS());
        $this->container->bind("RDev\\Views\\Cache\\ICache", $cache);
        $this->container->bind("RDev\\Views\\Compilers\\ICompiler", $compiler);
        $this->container->bind("RDev\\Views\\Factories\\ITemplateFactory", $templateFactory);

        // If we're developing, wipe out the view cache
        if($this->environment->getName() == Environments\Environment::DEVELOPMENT)
        {
            $cache->flush();
        }
    }
}
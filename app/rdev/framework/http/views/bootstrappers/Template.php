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
use RDev\Views\Factories;
use RDev\Views\Filters;

class Template extends Bootstrappers\Bootstrapper
{
    /**
     * {@inheritdoc}
     */
    public function registerBindings(IoC\IContainer $container)
    {
        $cache = $container->makeShared("RDev\\Views\\Cache\\Cache");
        $templateFactory = $container->makeShared("RDev\\Views\\Factories\\TemplateFactory");
        $compiler = new Compilers\Compiler($cache, $templateFactory, new Filters\XSS());
        $container->bind("RDev\\Views\\Cache\\ICache", $cache);
        // Bind to the concrete class, too
        $container->bind("RDev\\Views\\Cache\\Cache", $cache);
        $container->bind("RDev\\Views\\Compilers\\ICompiler", $compiler);
        $container->bind("RDev\\Views\\Factories\\ITemplateFactory", $templateFactory);
        // Bind to the concrete class, too
        $container->bind("RDev\\Views\\Factories\\TemplateFactory", $templateFactory);
    }

    /**
     * Finishes setting necessary properties for template components
     *
     * @param Cache\Cache $cache The view cache
     * @param Factories\TemplateFactory $templateFactory The template factory
     * @param Environments\Environment $environment The application environment
     * @param Framework\Paths $paths The application paths
     */
    public function run(
        Cache\Cache $cache,
        Factories\TemplateFactory $templateFactory,
        Environments\Environment $environment,
        Framework\Paths $paths
    )
    {
        // It does look like we're simply binding and that this should go in registerBindings()
        // However, we do need Paths to be bound before we can register the bindings, so it must go in run()
        /** @var Cache\Cache $cache */
        $cache->setPath($paths["compiledViews"]);
        $templateFactory->setRootTemplateDirectory($paths["views"]);

        // If we're developing, wipe out the view cache
        if($environment->getName() == Environments\Environment::DEVELOPMENT)
        {
            $cache->flush();
        }
    }
}
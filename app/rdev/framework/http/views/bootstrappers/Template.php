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
    /** @var Cache\Cache The view cache */
    private $viewCache;
    /** @var Factories\TemplateFactory The template factory */
    private $templateFactory;

    /**
     * {@inheritdoc}
     */
    public function registerBindings(IoC\IContainer $container)
    {
        $this->viewCache = $container->makeShared("RDev\\Views\\Cache\\Cache");
        $this->templateFactory = $container->makeShared("RDev\\Views\\Factories\\TemplateFactory");
        $compiler = new Compilers\Compiler($this->viewCache, $this->templateFactory, new Filters\XSS());
        $container->bind("RDev\\Views\\Cache\\ICache", $this->viewCache);
        // Bind to the concrete class, too
        $container->bind("RDev\\Views\\Cache\\Cache", $this->viewCache);
        $container->bind("RDev\\Views\\Compilers\\ICompiler", $compiler);
        $container->bind("RDev\\Views\\Factories\\ITemplateFactory", $this->templateFactory);
        // Bind to the concrete class, too
        $container->bind("RDev\\Views\\Factories\\TemplateFactory", $this->templateFactory);
    }

    /**
     * Finishes setting necessary properties for template components
     *
     * @param Environments\Environment $environment The application environment
     * @param Framework\Paths $paths The application paths
     */
    public function run(Environments\Environment $environment, Framework\Paths $paths)
    {
        // It does look like we're simply binding and that this should go in registerBindings()
        // However, we do need Paths to be bound before we can register the bindings, so it must go in run()
        $this->viewCache->setPath($paths["compiledViews"]);
        $this->templateFactory->setRootTemplateDirectory($paths["views"]);

        // If we're developing, wipe out the view cache
        if($environment->getName() == Environments\Environment::DEVELOPMENT)
        {
            $this->viewCache->flush();
        }
    }
}
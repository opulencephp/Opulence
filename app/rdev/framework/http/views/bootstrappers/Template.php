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
    private $viewCache = null;

    /**
     * {@inheritdoc}
     */
    public function registerBindings(IoC\IContainer $container)
    {
        $fileSystem = $container->makeShared("RDev\\Files\\FileSystem");
        $this->viewCache = new Cache\Cache($fileSystem, $this->paths["compiledViews"], 3600, 1, 100);
        $templateFactory = new Factories\TemplateFactory($fileSystem, $this->paths["views"]);
        $compiler = new Compilers\Compiler($this->viewCache, $templateFactory, new Filters\XSS());
        $container->bind("RDev\\Views\\Cache\\ICache", $this->viewCache);
        // Bind to the concrete class, too
        $container->bind("RDev\\Views\\Cache\\Cache", $this->viewCache);
        $container->bind("RDev\\Views\\Compilers\\ICompiler", $compiler);
        $container->bind("RDev\\Views\\Factories\\ITemplateFactory", $templateFactory);
        // Bind to the concrete class, too
        $container->bind("RDev\\Views\\Factories\\TemplateFactory", $templateFactory);
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
}
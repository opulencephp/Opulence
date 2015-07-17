<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the template bootstrapper
 */
namespace Opulence\Framework\Bootstrappers\HTTP\Views;
use Opulence\Applications\Bootstrappers\Bootstrapper;
use Opulence\Applications\Bootstrappers\ILazyBootstrapper;
use Opulence\Applications\Environments\Environment;
use Opulence\Files\FileSystem;
use Opulence\IoC\IContainer;
use Opulence\Views\Caching\ICache;
use Opulence\Views\Compilers\Compiler;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Factories\ITemplateFactory;
use Opulence\Views\Factories\TemplateFactory;
use Opulence\Views\Filters\XSSFilter;

abstract class Template extends Bootstrapper implements ILazyBootstrapper
{
    /** @var ICache The view cache */
    protected $viewCache = null;
    /** @var ITemplateFactory The template factory */
    protected $templateFactory = null;

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        return [ICache::class, ICompiler::class, ITemplateFactory::class];
    }

    /**
     * {@inheritdoc}
     */
    public function registerBindings(IContainer $container)
    {
        $this->viewCache = $this->getViewCache($container);
        $this->templateFactory = $this->getTemplateFactory($container);
        $compiler = $this->getViewCompiler($container);
        $container->bind(ICache::class, $this->viewCache);
        $container->bind(ICompiler::class, $compiler);
        $container->bind(ITemplateFactory::class, $this->templateFactory);
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
     * Gets the view cache
     * To use a different view cache than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     * @return ICache The view cache
     */
    abstract protected function getViewCache(IContainer $container);

    /**
     * Gets the view template factory
     * To use a different template factory than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     * @return ITemplateFactory The template factory
     */
    protected function getTemplateFactory(IContainer $container)
    {
        $fileSystem = $container->makeShared(FileSystem::class);

        return new TemplateFactory($fileSystem, $this->paths["views.raw"]);
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
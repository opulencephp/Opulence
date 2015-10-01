<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the view bootstrapper
 */
namespace Opulence\Framework\Bootstrappers\HTTP\Views;

use Opulence\Applications\Bootstrappers\Bootstrapper;
use Opulence\Applications\Bootstrappers\ILazyBootstrapper;
use Opulence\Applications\Environments\Environment;
use Opulence\Files\FileSystem;
use Opulence\IoC\IContainer;
use Opulence\Views\Caching\ICache;
use Opulence\Views\Compilers\Compiler;
use Opulence\Views\Compilers\CompilerRegistry;
use Opulence\Views\Compilers\Fortune\FortuneCompiler;
use Opulence\Views\Compilers\Fortune\ITranspiler;
use Opulence\Views\Compilers\Fortune\Lexers\Lexer;
use Opulence\Views\Compilers\Fortune\Parsers\Parser;
use Opulence\Views\Compilers\Fortune\Transpiler;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Compilers\PHP\PHPCompiler;
use Opulence\Views\Factories\FileViewNameResolver;
use Opulence\Views\Factories\IViewFactory;
use Opulence\Views\Factories\IViewNameResolver;
use Opulence\Views\Factories\ViewFactory;
use Opulence\Views\Filters\XSSFilter;

abstract class View extends Bootstrapper implements ILazyBootstrapper
{
    /** @var ICache The view cache */
    protected $viewCache = null;
    /** @var IViewFactory The view factory */
    protected $viewFactory = null;

    /**
     * @inheritdoc
     */
    public function getBindings()
    {
        return [ICache::class, ICompiler::class, ITranspiler::class, IViewFactory::class, IViewNameResolver::class];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $this->viewCache = $this->getViewCache($container);
        $this->viewFactory = $this->getViewFactory($container);
        $compiler = $this->getViewCompiler($container);
        $container->bind(ICompiler::class, $compiler);
        $container->bind(ICache::class, $this->viewCache);
        $container->bind(IViewFactory::class, $this->viewFactory);
    }

    /**
     * Finishes setting necessary properties for view components
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
     * Gets the view compiler
     * To use a different view compiler than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     * @return ICompiler The view compiler
     */
    protected function getViewCompiler(IContainer $container)
    {
        $registry = new CompilerRegistry();
        $viewCompiler = new Compiler($registry);

        // Setup our various sub-compilers
        $transpiler = new Transpiler(new Lexer(), new Parser(), $this->viewCache, new XSSFilter());
        $container->bind(ITranspiler::class, $transpiler);
        $fortuneCompiler = new FortuneCompiler($transpiler, $this->viewFactory);
        $registry->registerCompiler("fortune", $fortuneCompiler);
        $registry->registerCompiler("php", new PHPCompiler());

        return $viewCompiler;
    }

    /**
     * Gets the view view factory
     * To use a different view factory than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     * @return IViewFactory The view factory
     */
    protected function getViewFactory(IContainer $container)
    {
        $resolver = new FileViewNameResolver();
        $resolver->registerPath($this->paths["views.raw"]);
        $resolver->registerExtension("php");
        $resolver->registerExtension("fortune");
        $container->bind(IViewNameResolver::class, $resolver);

        return new ViewFactory($resolver, $container->makeShared(FileSystem::class));
    }
}
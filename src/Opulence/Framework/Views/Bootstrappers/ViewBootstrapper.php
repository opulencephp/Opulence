<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Views\Bootstrappers;

use Opulence\Environments\Environment;
use Opulence\Framework\Configuration\Config;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Views\Caching\ICache;
use Opulence\Views\Compilers\Compiler;
use Opulence\Views\Compilers\CompilerRegistry;
use Opulence\Views\Compilers\Fortune\FortuneCompiler;
use Opulence\Views\Compilers\Fortune\ITranspiler;
use Opulence\Views\Compilers\Fortune\Lexers\Lexer;
use Opulence\Views\Compilers\Fortune\Parsers\Parser;
use Opulence\Views\Compilers\Fortune\Transpiler;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Compilers\Php\PhpCompiler;
use Opulence\Views\Factories\IO\FileViewNameResolver;
use Opulence\Views\Factories\IO\FileViewReader;
use Opulence\Views\Factories\IO\IViewNameResolver;
use Opulence\Views\Factories\IO\IViewReader;
use Opulence\Views\Factories\IViewFactory;
use Opulence\Views\Factories\ViewFactory;
use Opulence\Views\Filters\XssFilter;

/**
 * Defines the view bootstrapper
 */
abstract class ViewBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /** @var ICache The view cache */
    protected $viewCache = null;
    /** @var IViewFactory The view factory */
    protected $viewFactory = null;

    /**
     * @inheritdoc
     */
    public function getBindings() : array
    {
        return [
            ICache::class,
            ICompiler::class,
            ITranspiler::class,
            IViewFactory::class,
            IViewReader::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $this->viewCache = $this->getViewCache($container);
        $this->viewFactory = $this->getViewFactory($container);
        $compiler = $this->getViewCompiler($container);
        $container->bindInstance(ICompiler::class, $compiler);
        $container->bindInstance(ICache::class, $this->viewCache);
        $container->bindInstance(IViewFactory::class, $this->viewFactory);

        // If we're developing, wipe out the view cache
        if (getenv('ENV_NAME') === Environment::DEVELOPMENT) {
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
    abstract protected function getViewCache(IContainer $container) : ICache;

    /**
     * Gets the view compiler
     * To use a different view compiler than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     * @return ICompiler The view compiler
     */
    protected function getViewCompiler(IContainer $container) : ICompiler
    {
        $registry = new CompilerRegistry();
        $viewCompiler = new Compiler($registry);

        // Setup our various sub-compilers
        $transpiler = new Transpiler(new Lexer(), new Parser(), $this->viewCache, new XssFilter());
        $container->bindInstance(ITranspiler::class, $transpiler);
        $fortuneCompiler = new FortuneCompiler($transpiler, $this->viewFactory);
        $registry->registerCompiler('fortune', $fortuneCompiler);
        $registry->registerCompiler('fortune.php', $fortuneCompiler);
        $registry->registerCompiler('php', new PhpCompiler());

        return $viewCompiler;
    }

    /**
     * Gets the view view factory
     * To use a different view factory than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     * @return IViewFactory The view factory
     */
    protected function getViewFactory(IContainer $container) : IViewFactory
    {
        $resolver = new FileViewNameResolver();
        $resolver->registerPath(Config::get('paths', 'views.raw'));
        $resolver->registerExtension('fortune');
        $resolver->registerExtension('fortune.php');
        $resolver->registerExtension('php');
        $viewReader = $this->getViewReader($container);
        $container->bindInstance(IViewNameResolver::class, $resolver);
        $container->bindInstance(IViewReader::class, $viewReader);

        return new ViewFactory($resolver, $viewReader);
    }

    /**
     * Gets the view reader
     * To use a different view reader than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     * @return IViewReader The view reader
     */
    protected function getViewReader(IContainer $container) : IViewReader
    {
        return new FileViewReader();
    }
}

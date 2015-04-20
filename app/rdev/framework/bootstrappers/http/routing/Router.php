<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the routing bootstrapper
 */
namespace RDev\Framework\Bootstrappers\HTTP\Routing;
use RDev\Applications\Bootstrappers\Bootstrapper;
use RDev\IoC\IContainer;
use RDev\Routing\Router as HTTPRouter;
use RDev\Routing\Compilers\Compiler;
use RDev\Routing\Compilers\ICompiler;
use RDev\Routing\Compilers\Parsers\IParser;
use RDev\Routing\Compilers\Parsers\Parser;
use RDev\Routing\Dispatchers\Dispatcher;
use RDev\Routing\Dispatchers\IDispatcher;
use RDev\Routing\URL\URLGenerator;

class Router extends Bootstrapper
{
    /** @var IParser The route parser */
    protected $parser = null;

    /**
     * {@inheritdoc}
     */
    public function registerBindings(IContainer $container)
    {
        $dispatcher = $this->getRouteDispatcher($container);
        $this->parser = $this->getRouteParser($container);
        $compiler = $this->getRouteCompiler($container);
        $router = new HTTPRouter($dispatcher, $compiler);
        $urlGenerator = new URLGenerator($router->getRouteCollection(), $this->parser);
        $container->bind("RDev\\Routing\\Dispatchers\\IDispatcher", $dispatcher);
        $container->bind("RDev\\Routing\\Compilers\\ICompiler", $compiler);
        $container->bind("RDev\\Routing\\Router", $router);
        $container->bind("RDev\\Routing\\URL\\URLGenerator", $urlGenerator);
    }

    /**
     * Gets the route compiler
     * To use a different route compiler than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     * @return ICompiler The route compiler
     */
    protected function getRouteCompiler(IContainer $container)
    {
        return new Compiler($this->parser);
    }

    /**
     * Gets the route dispatcher
     * To use a different route dispatcher than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     * @return IDispatcher The route dispatcher
     */
    protected function getRouteDispatcher(IContainer $container)
    {
        return new Dispatcher($container);
    }

    /**
     * Gets the route parser
     * To use a different route parser than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     * @return IParser The route parser
     */
    protected function getRouteParser(IContainer $container)
    {
        return new Parser();
    }
}
<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the routing bootstrapper
 */
namespace RDev\Framework\Bootstrappers\HTTP\Routing;
use RDev\Applications\Bootstrappers;
use RDev\HTTP\Routing;
use RDev\HTTP\Routing\Compilers;
use RDev\HTTP\Routing\Compilers\Parsers;
use RDev\HTTP\Routing\Dispatchers;
use RDev\HTTP\Routing\URL;
use RDev\IoC;

class Router extends Bootstrappers\Bootstrapper
{
    /** @var Parsers\IParser The route parser */
    protected $parser = null;

    /**
     * {@inheritdoc}
     */
    public function registerBindings(IoC\IContainer $container)
    {
        $dispatcher = $this->getRouteDispatcher($container);
        $this->parser = $this->getRouteParser($container);
        $compiler = $this->getRouteCompiler($container);
        $router = new Routing\Router($dispatcher, $compiler);
        $urlGenerator = new URL\URLGenerator($router->getRoutes(), $this->parser);
        $container->bind("RDev\\HTTP\\Routing\\Compilers\\ICompiler", $compiler);
        $container->bind("RDev\\HTTP\\Routing\\Router", $router);
        $container->bind("RDev\\HTTP\\Routing\\URL\\URLGenerator", $urlGenerator);
    }

    /**
     * Gets the route compiler
     * To use a different route compiler than the one returned here, extend this class and override this method
     *
     * @param IoC\IContainer $container The dependency injection container
     * @return Compilers\ICompiler The route compiler
     */
    protected function getRouteCompiler(IoC\IContainer $container)
    {
        return new Compilers\Compiler($this->parser);
    }

    /**
     * Gets the route dispatcher
     * To use a different route dispatcher than the one returned here, extend this class and override this method
     *
     * @param IoC\IContainer $container The dependency injection container
     * @return Dispatchers\IDispatcher The route dispatcher
     */
    protected function getRouteDispatcher(IoC\IContainer $container)
    {
        return new Dispatchers\Dispatcher($container);
    }

    /**
     * Gets the route parser
     * To use a different route parser than the one returned here, extend this class and override this method
     *
     * @param IoC\IContainer $container The dependency injection container
     * @return Parsers\IParser The route parser
     */
    protected function getRouteParser(IoC\IContainer $container)
    {
        return new Parsers\Parser();
    }
}
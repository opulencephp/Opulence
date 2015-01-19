<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the routing bootstrapper
 */
namespace RDev\Framework\HTTP\Routing\Bootstrappers;
use RDev\Applications\Bootstrappers;
use RDev\HTTP\Routing;
use RDev\HTTP\Routing\Compilers;
use RDev\HTTP\Routing\Compilers\Parsers;
use RDev\HTTP\Routing\Dispatchers;
use RDev\HTTP\Routing\URL;
use RDev\IoC;

class Router implements Bootstrappers\IBootstrapper
{
    /** @var IoC\IContainer The dependency injection container to use */
    private $container = null;

    /**
     * @param IoC\IContainer $container The dependency injection container to use
     */
    public function __construct(IoC\IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $dispatcher = new Dispatchers\Dispatcher($this->container);
        $parser = new Parsers\Parser();
        $compiler = new Compilers\Compiler($parser);
        $router = new Routing\Router(
            $dispatcher,
            $compiler,
            "Project\\HTTP\\Controllers\\Page"
        );
        $urlGenerator = new URL\URLGenerator($router->getRoutes(), $parser);
        $this->container->bind("RDev\\HTTP\\Routing\\URL\\URLGenerator", $urlGenerator);
        $this->container->bind("RDev\\HTTP\\Routing\\Router", $router);
        $this->container->bind("RDev\\HTTP\\Routing\\Compilers\\ICompiler", $compiler);
    }
}
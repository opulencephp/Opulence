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

class Router extends Bootstrappers\Bootstrapper
{
    /**
     * {@inheritdoc}
     */
    public function registerBindings(IoC\IContainer $container)
    {
        $dispatcher = new Dispatchers\Dispatcher($container);
        $parser = new Parsers\Parser();
        $compiler = new Compilers\Compiler($parser);
        $router = new Routing\Router($dispatcher, $compiler);
        $urlGenerator = new URL\URLGenerator($router->getRoutes(), $parser);
        $container->bind("RDev\\HTTP\\Routing\\URL\\URLGenerator", $urlGenerator);
        $container->bind("RDev\\HTTP\\Routing\\Router", $router);
        $container->bind("RDev\\HTTP\\Routing\\Compilers\\ICompiler", $compiler);
    }
}
<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the router for use in testing
 */
namespace RDev\Tests\Routing\Mocks;
use RDev\HTTP;
use RDev\IoC;
use RDev\Routing;

class Router extends Routing\Router
{
    /**
     * {@inheritdoc}
     */
    public function __construct(IoC\IContainer $container)
    {
        parent::__construct($container, new Dispatcher($container), new Routing\RouteCompiler());
    }
} 
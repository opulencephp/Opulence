<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the router for use in testing
 */
namespace RDev\Tests\Models\Routing\Mocks;
use RDev\Models\HTTP;
use RDev\Models\IoC;
use RDev\Models\Routing;

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
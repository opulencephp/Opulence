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
    public function __construct()
    {
        parent::__construct(new Dispatcher(new IoC\Container()), new Routing\RouteCompiler());
    }
} 
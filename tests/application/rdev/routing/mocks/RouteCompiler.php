<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the route compiler for use in testing
 */
namespace RDev\Tests\Routing\Mocks;
use RDev\Routing;
use RDev\Routing\Route;

class RouteCompiler implements Routing\IRouteCompiler
{
    /**
     * {@inheritdoc}
     */
    public function compile(Route &$route)
    {
        $route->setPathRegex("/^foo$/");
    }
} 
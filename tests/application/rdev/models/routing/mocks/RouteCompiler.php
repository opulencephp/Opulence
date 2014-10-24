<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the route compiler for use in testing
 */
namespace RDev\Tests\Models\Routing\Mocks;
use RDev\Models\Routing;
use RDev\Models\Routing\Route;

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
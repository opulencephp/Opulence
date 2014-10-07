<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the dispatcher for use in testing
 */
namespace RDev\Tests\Models\Routing\Mocks;
use RDev\Models\Routing;
use RDev\Models\Routing\Route;

class Dispatcher extends Routing\Dispatcher
{
    /**
     * For the sake of testing the router, simply return the dispatched route rather than its output
     * This makes it easy to test that the router is selecting the correct route
     *
     * @param Route $route The route to be dispatched
     * @param array $routeVariables The list of route variables
     * @return Route The chosen route
     */
    public function dispatch(Route $route, array $routeVariables)
    {
        return $route;
    }
} 
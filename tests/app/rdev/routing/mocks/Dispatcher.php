<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the dispatcher for use in testing
 */
namespace RDev\Tests\Routing\Mocks;
use RDev\HTTP;
use RDev\Routing;
use RDev\Routing\Route;

class Dispatcher extends Routing\Dispatcher
{
    /**
     * For the sake of testing the router, simply return the dispatched route rather than its output
     * This makes it easy to test that the router is selecting the correct route
     *
     * @param Route $route The route to be dispatched
     * @param HTTP\Request $request The request made by the user
     * @param array $routeVariables The list of route variables
     * @return Route The chosen route
     */
    public function dispatch(Route $route, HTTP\Request $request, array $routeVariables)
    {
        return $route;
    }
} 
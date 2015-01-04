<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the dispatcher for use in testing
 */
namespace RDev\Tests\Routing\Dispatchers\Mocks;
use RDev\HTTP;
use RDev\Routing\Dispatchers;
use RDev\Routing\Routes;

class Dispatcher extends Dispatchers\Dispatcher
{
    /**
     * For the sake of testing the router, simply return the dispatched route rather than its output
     * This makes it easy to test that the router is selecting the correct route
     *
     * @param Routes\CompiledRoute $route The route to be dispatched
     * @param HTTP\Request $request The request made by the user
     * @return Routes\Route The chosen route
     */
    public function dispatch(Routes\CompiledRoute $route, HTTP\Request $request)
    {
        return $route;
    }
} 
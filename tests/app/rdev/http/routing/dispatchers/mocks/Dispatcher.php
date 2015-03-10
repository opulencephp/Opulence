<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the dispatcher for use in testing
 */
namespace RDev\Tests\Routing\Dispatchers\Mocks;
use RDev\HTTP\Requests;
use RDev\HTTP\Routing;
use RDev\HTTP\Routing\Dispatchers;
use RDev\HTTP\Routing\Routes;

class Dispatcher extends Dispatchers\Dispatcher
{
    /**
     * For the sake of testing the router, simply return the dispatched route rather than its output
     * This makes it easy to test that the router is selecting the correct route
     *
     * @param Routes\CompiledRoute $route The route to be dispatched
     * @param Requests\Request $request The request made by the user
     * @param Routing\Controller|null $controller Will be set to the instance of the controller that was matched
     * @return Routes\Route The chosen route
     */
    public function dispatch(Routes\CompiledRoute $route, Requests\Request $request, Routing\Controller &$controller = null)
    {
        $controller = new Routing\Controller();

        return $route;
    }
} 
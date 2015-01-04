<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for dispatchers to implement
 */
namespace RDev\Routing\Dispatchers;
use RDev\HTTP;
use RDev\Routing;
use RDev\Routing\Routes;

interface IDispatcher 
{
    /**
     * Dispatches the input route
     *
     * @param Routes\CompiledRoute $route The route to dispatch
     * @param HTTP\Request $request The request made by the user
     * @return HTTP\Response The response from the controller or pre/post filters if there was one
     * @throws Routing\RouteException Thrown if the method could not be called on the controller
     */
    public function dispatch(Routes\CompiledRoute $route, HTTP\Request $request);
}
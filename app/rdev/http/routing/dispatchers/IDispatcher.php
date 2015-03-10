<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for dispatchers to implement
 */
namespace RDev\HTTP\Routing\Dispatchers;
use RDev\HTTP\Requests;
use RDev\HTTP\Responses;
use RDev\HTTP\Routing;
use RDev\HTTP\Routing\Routes;

interface IDispatcher 
{
    /**
     * Dispatches the input route
     *
     * @param Routes\CompiledRoute $route The route to dispatch
     * @param Requests\Request $request The request made by the user
     * @param Routing\Controller|null $controller Will be set to the instance of the controller that was matched
     * @return Responses\Response The response from the controller or pre/post filters if there was one
     * @throws Routing\RouteException Thrown if the method could not be called on the controller
     */
    public function dispatch(Routes\CompiledRoute $route, Requests\Request $request, Routing\Controller &$controller = null);
}
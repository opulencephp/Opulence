<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for dispatchers to implement
 */
namespace RDev\HTTP\Routing\Dispatchers;
use RDev\HTTP\Requests\Request;
use RDev\HTTP\Responses\Response;
use RDev\HTTP\Routing\Controller;
use RDev\HTTP\Routing\Routes\CompiledRoute;
use RDev\HTTP\Routing\RouteException;

interface IDispatcher 
{
    /**
     * Dispatches the input route
     *
     * @param CompiledRoute $route The route to dispatch
     * @param Request $request The request made by the user
     * @param Controller|null $controller Will be set to the instance of the controller that was matched
     * @return Response The response from the controller or pre/post filters if there was one
     * @throws RouteException Thrown if the method could not be called on the controller
     */
    public function dispatch(CompiledRoute $route, Request $request, Controller &$controller = null);
}
<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for dispatchers to implement
 */
namespace Opulence\Routing\Dispatchers;

use Opulence\HTTP\Requests\Request;
use Opulence\HTTP\Responses\Response;
use Opulence\Routing\Controller;
use Opulence\Routing\RouteException;
use Opulence\Routing\Routes\CompiledRoute;

interface IDispatcher
{
    /**
     * Dispatches the input route
     *
     * @param CompiledRoute $route The route to dispatch
     * @param Request $request The request made by the user
     * @param Controller|mixed|null $controller Will be set to the instance of the controller that was matched
     * @return Response The response from the controller or pre/post filters if there was one
     * @throws RouteException Thrown if the method could not be called on the controller
     */
    public function dispatch(CompiledRoute $route, Request $request, &$controller = null);
}
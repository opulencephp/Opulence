<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a route that is dispatched when the router misses on a path
 */
namespace RDev\Routing\Routes;
use RDev\HTTP;

class MissingRoute extends CompiledRoute
{
    /**
     * @param string $controllerClass The name of the controller to call
     */
    public function __construct($controllerClass)
    {
        $methods = [
            HTTP\Request::METHOD_DELETE,
            HTTP\Request::METHOD_GET,
            HTTP\Request::METHOD_POST,
            HTTP\Request::METHOD_PUT,
            HTTP\Request::METHOD_HEAD,
            HTTP\Request::METHOD_TRACE,
            HTTP\Request::METHOD_PURGE,
            HTTP\Request::METHOD_CONNECT,
            HTTP\Request::METHOD_PATCH,
            HTTP\Request::METHOD_OPTIONS
        ];
        $route = new Route($methods, "", ["controller" => $controllerClass . "@showHTTPError"]);
        parent::__construct(new ParsedRoute($route), true);

        $this->setDefaultValue("statusCode", HTTP\ResponseHeaders::HTTP_NOT_FOUND);
    }
}
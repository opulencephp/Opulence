<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a list of routes that can be used by a router
 */
namespace RDev\HTTP\Routing\Routes;
use RDev\HTTP\Requests;

class Routes
{
    /** @var array The list of methods */
    private static $methods = [
        Requests\Request::METHOD_DELETE,
        Requests\Request::METHOD_GET,
        Requests\Request::METHOD_POST,
        Requests\Request::METHOD_PUT,
        Requests\Request::METHOD_HEAD,
        Requests\Request::METHOD_OPTIONS,
        Requests\Request::METHOD_PATCH
    ];
    /** @var array The list of methods to their various routes */
    private $routes = [];
    /** @var Route[] The mapping of route names to routes */
    private $namedRoutes = [];

    public function __construct()
    {
        foreach(self::$methods as $method)
        {
            $this->routes[$method] = [];
        }
    }

    /**
     * Gets the list of methods
     *
     * @return array The list of methods
     */
    public static function getMethods()
    {
        return self::$methods;
    }

    /**
     * Adds a route to the collection
     *
     * @param Route $route The route to add
     */
    public function add(Route $route)
    {
        foreach($route->getMethods() as $method)
        {
            $this->routes[$method][] = $route;

            if(!empty($route->getName()))
            {
                $this->namedRoutes[$route->getName()] =& $route;
            }
        }
    }

    /**
     * Gets all the routes
     *
     * @param string|null $method If specified, the list of routes for that method will be returned
     *      If null, all routes will be returned, keyed by method
     * @return Route[] The list of routes
     */
    public function get($method = null)
    {
        if($method === null)
        {
            return $this->routes;
        }
        elseif(isset($this->routes[$method]))
        {
            return $this->routes[$method];
        }
        else
        {
            return [];
        }
    }

    /**
     * Gets the route with the input name
     *
     * @param string $name The name to search for
     * @return Route|null The route with the input name if one existed, otherwise null
     */
    public function getNamedRoute($name)
    {
        if(isset($this->namedRoutes[$name]))
        {
            return $this->namedRoutes[$name];
        }

        return null;
    }
}
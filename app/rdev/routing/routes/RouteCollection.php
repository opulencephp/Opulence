<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a list of routes that can be used by a router
 */
namespace RDev\Routing\Routes;
use RDev\HTTP\Requests\Request;

class RouteCollection
{
    /** @var array The list of methods */
    private static $methods = [
        Request::METHOD_DELETE,
        Request::METHOD_GET,
        Request::METHOD_POST,
        Request::METHOD_PUT,
        Request::METHOD_HEAD,
        Request::METHOD_OPTIONS,
        Request::METHOD_PATCH
    ];
    /** @var array The list of methods to their various routes */
    private $routes = [];
    /** @var ParsedRoute[] The mapping of route names to routes */
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
     * @param ParsedRoute $route The route to add
     */
    public function add(ParsedRoute $route)
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
     * @return ParsedRoute[] The list of routes
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
     * @return ParsedRoute|null The route with the input name if one existed, otherwise null
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
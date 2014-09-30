<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a router for URL requests
 */
namespace RDev\Models\Web\Routing;
use RDev\Models\Web;

class Router
{
    /** @var IRouteCompiler The compiler used by this router */
    private $compiler = null;
    /** @var Web\HTTPConnection The HTTP connection */
    private $httpConnection = null;
    /** @var Route[] The list of methods to their various routes */
    private $routes = [
        Web\Request::METHOD_DELETE => [],
        Web\Request::METHOD_GET => [],
        Web\Request::METHOD_POST => [],
        Web\Request::METHOD_PUT => []
    ];

    /**
     * @param Web\HTTPConnection $httpConnection The HTTP connection
     * @param Configs\RouterConfig|array $config The configuration to use
     *      The following keys are optional:
     *          "compiler" => Name of class that implements IRouteCompiler or an instantiated object,
     *          "routes" => Array containing the following:
     *              "methods" => The HTTP methods matched by the request (eg "GET", "POST", ...),
     *              "path" => The path matched by the request,
     *              "options" => The optional array of route options, which may contain the following:
     *                  "variables" => The mapping of route-variable names to the regexes they must fulfill
     */
    public function __construct(Web\HTTPConnection $httpConnection, $config)
    {
        $this->httpConnection = $httpConnection;

        if(is_array($config))
        {
            $config = new Configs\RouterConfig($config);
        }

        $this->compiler = $config["compiler"];

        foreach($config["routes"] as $route)
        {
            $this->addRoute($route);
        }
    }

    /**
     * Adds a route for the DELETE method at the given path
     *
     * @param string $path The path to match on
     * @param array $options The list of options for this path
     */
    public function delete($path, array $options)
    {
        $route = $this->createRoute(Web\Request::METHOD_DELETE, $path, $options);
        $this->addRoute($route);
    }

    /**
     * Adds a route for the GET method at the given path
     *
     * @param string $path The path to match on
     * @param array $options The list of options for this path
     */
    public function get($path, array $options)
    {
        $route = $this->createRoute(Web\Request::METHOD_GET, $path, $options);
        $this->addRoute($route);
    }

    /**
     * Adds a route for the POST method at the given path
     *
     * @param string $path The path to match on
     * @param array $options The list of options for this path
     */
    public function post($path, array $options)
    {
        $route = $this->createRoute(Web\Request::METHOD_POST, $path, $options);
        $this->addRoute($route);
    }

    /**
     * Adds a route for the PUT method at the given path
     *
     * @param string $path The path to match on
     * @param array $options The list of options for this path
     */
    public function put($path, array $options)
    {
        $route = $this->createRoute(Web\Request::METHOD_PUT, $path, $options);
        $this->addRoute($route);
    }

    /**
     * Routes a path
     *
     * @param string $path The path to route
     * @throws \RuntimeException Thrown if there was a problem routing the input path
     */
    public function route($path)
    {
        $method = $this->httpConnection->getRequest()->getMethod();

        if(!isset($this->routes[$method]))
        {
            throw new \RuntimeException("No route specified for method $method");
        }

        /** @var Route $route */
        foreach($this->routes[$method] as $route)
        {
            $this->compiler->compile($route);
            $matches = [];

            if(preg_match($route->getRegex(), $path, $matches))
            {
                // TODO:  Actually dispatch the action on a controller
            }
        }
    }

    /**
     * Adds a route to the router
     *
     * @param Route $route The route to add
     */
    private function addRoute(Route $route)
    {
        foreach($route->getMethods() as $method)
        {
            $this->routes[$method][] = $route;
        }
    }

    /**
     * Creates a route from the input
     *
     * @param string $method The method whose route this is
     * @param string $path The path to match on
     * @param array $options The list of options for this path
     * @return Route The route from the input
     */
    private function createRoute($method, $path, array $options)
    {
        return new Route([$method], $path, $options);
    }
} 
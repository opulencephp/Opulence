<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a router for URL requests
 */
namespace RDev\Models\Routing;
use RDev\Models\HTTP;
use RDev\Models\IoC;

class Router
{
    /** @var IoC\IContainer The dependency injection container */
    protected $iocContainer = null;
    /** @var IRouteCompiler The compiler used by this router */
    protected $compiler = null;
    /** @var Dispatcher The route dispatcher */
    protected $dispatcher = null;
    /** @var HTTP\Connection The HTTP connection */
    protected $httpConnection = null;
    /** @var array The list of methods to their various routes */
    protected $routes = [
        HTTP\Request::METHOD_DELETE => [],
        HTTP\Request::METHOD_GET => [],
        HTTP\Request::METHOD_POST => [],
        HTTP\Request::METHOD_PUT => []
    ];

    /**
     * @param IoC\IContainer $iocContainer The dependency injection container
     * @param HTTP\Connection $httpConnection The HTTP connection
     * @param Configs\RouterConfig|array $config The configuration to use
     *      The following keys are optional:
     *          "compiler" => Name of class that implements IRouteCompiler or an instantiated object,
     *          "routes" => Array containing the following:
     *              "methods" => The HTTP methods matched by the request (eg "GET", "POST", ...),
     *              "path" => The path matched by the request,
     *              "options" => The optional array of route options, which may contain the following:
     *                  "variables" => The mapping of route-variable names to the regexes they must fulfill
     */
    public function __construct(IoC\IContainer $iocContainer, HTTP\Connection $httpConnection, $config = [])
    {
        $this->iocContainer = $iocContainer;
        $this->httpConnection = $httpConnection;
        $this->dispatcher = new Dispatcher($this->iocContainer);

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
     * Adds a route to the router
     *
     * @param Route $route The route to add
     */
    public function addRoute(Route $route)
    {
        foreach($route->getMethods() as $method)
        {
            $this->routes[$method][] = $route;
        }
    }

    /**
     * Adds a route for the any method at the given path
     *
     * @param string $path The path to match on
     * @param array $options The list of options for this path
     */
    public function any($path, array $options)
    {
        $this->multiple(array_keys($this->routes), $path, $options);
    }

    /**
     * Adds a route for the DELETE method at the given path
     *
     * @param string $path The path to match on
     * @param array $options The list of options for this path
     */
    public function delete($path, array $options)
    {
        $route = $this->createRoute(HTTP\Request::METHOD_DELETE, $path, $options);
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
        $route = $this->createRoute(HTTP\Request::METHOD_GET, $path, $options);
        $this->addRoute($route);
    }

    /**
     * Gets all the routes in this router
     *
     * @param string|null $method If specified, the list of routes for that method will be returned
     *      If null, all routes will be returned, keyed by method
     * @return array The list of routes
     */
    public function getRoutes($method = null)
    {
        if($method === null)
        {
            return $this->routes;
        }
        elseif(!isset($this->routes[$method]))
        {
            return [];
        }
        else
        {
            return $this->routes[$method];
        }
    }

    /**
     * Adds a route for multiple methods
     *
     * @param array $methods The list of methods to match on
     * @param string $path The path to match on
     * @param array $options The list of options for this path
     */
    public function multiple(array $methods, $path, array $options)
    {
        foreach($methods as $method)
        {
            $route = $this->createRoute($method, $path, $options);
            $this->addRoute($route);
        }
    }

    /**
     * Adds a route for the POST method at the given path
     *
     * @param string $path The path to match on
     * @param array $options The list of options for this path
     */
    public function post($path, array $options)
    {
        $route = $this->createRoute(HTTP\Request::METHOD_POST, $path, $options);
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
        $route = $this->createRoute(HTTP\Request::METHOD_PUT, $path, $options);
        $this->addRoute($route);
    }

    /**
     * Registers a filter function that can be used before/after a request
     *
     * @param string $name The name of the filter
     * @param callable $callback The callback that executes custom logic
     */
    public function registerFilter($name, callable $callback)
    {
        $this->dispatcher->registerFilter($name, $callback);
    }

    /**
     * Routes a path
     *
     * @param string $path The path to route
     * @return mixed The return value of the controller
     * @throws \RuntimeException Thrown if there was a problem routing the input path
     * @throws Exceptions\RouteException Thrown if the controller or method could not be called
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
                return $this->dispatcher->dispatch($route, $matches);
            }
        }

        // TODO: Implement a default controller
        return "NOTHING";
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
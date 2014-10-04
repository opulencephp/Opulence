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
    private $iocContainer = null;
    /** @var IRouteCompiler The compiler used by this router */
    private $compiler = null;
    /** @var HTTP\Connection The HTTP connection */
    private $httpConnection = null;
    /** @var array The list of methods to their various routes */
    private $routes = [
        HTTP\Request::METHOD_DELETE => [],
        HTTP\Request::METHOD_GET => [],
        HTTP\Request::METHOD_POST => [],
        HTTP\Request::METHOD_PUT => []
    ];
    /** @var callable[] The list of filters that can be used */
    private $filters = [];

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
    public function __construct(IoC\IContainer $iocContainer, HTTP\Connection $httpConnection, $config)
    {
        $this->iocContainer = $iocContainer;
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
        $this->filters[$name] = $callback;
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
                // Do our pre-filters
                if(($preFilterReturnValue = $this->executeFilters($route->getPreFilters())) !== null)
                {
                    return $preFilterReturnValue;
                }

                // Call our controller
                if(($controllerResponse = $this->callController($route, $matches)) !== null)
                {
                    return $controllerResponse;
                }

                // Do our post-filters
                if(($postFilterReturnValue = $this->executeFilters($route->getPostFilters())) !== null)
                {
                    return $postFilterReturnValue;
                }
            }
        }

        // TODO: Implement a default controller
        return "NOTHING";
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
     * Calls the controller for a matched route
     *
     * @param Route $route The matched route
     * @param array $matches The list of matches on route variables
     * @return mixed|null The response from the controller, if there was one, otherwise null
     * @throws Exceptions\RouteException Thrown if the controller or method were invalid
     */
    private function callController(Route $route, array $matches)
    {
        $controllerName = $route->getControllerName();
        $method = $route->getControllerMethod();
        $parameters = [];

        if(!class_exists($controllerName))
        {
            throw new Exceptions\RouteException("Controller class $controllerName does not exist");
        }

        try
        {
            $reflection = new \ReflectionMethod($controllerName, $method);

            if(!$reflection->isPublic())
            {
                throw new Exceptions\RouteException("Method $method is not public");
            }

            // Match the route variables to the method parameters
            foreach($reflection->getParameters() as $parameter)
            {
                if(isset($matches[$parameter->getName()]))
                {
                    $parameters[$parameter->getPosition()] = $matches[$parameter->getName()];
                }
                elseif(!$parameter->isDefaultValueAvailable())
                {
                    throw new Exceptions\RouteException(
                        "No value set for parameter {$parameter->getName()}"
                    );
                }
            }

            $controller = $this->iocContainer->createSingleton($controllerName);

            return call_user_func_array([$controller, $method], $parameters);
        }
        catch(\ReflectionException $ex)
        {
            throw new Exceptions\RouteException(
                "Reflection failed for method $method in controller $controllerName: $ex"
            );
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

    /**
     * Executes the list of filters
     *
     * @param array $filterNames The list of filter names to execute
     * @return mixed|null The response from any of the filters if they returned something, otherwise null
     * @throws Exceptions\RouteException Thrown if the filter does not exist
     */
    private function executeFilters(array $filterNames)
    {
        foreach($filterNames as $filterName)
        {
            if(!isset($this->filters[$filterName]))
            {
                throw new Exceptions\RouteException("Filter $filterName is not registered with the router");
            }

            if(($filterReturnValue = $this->filters[$filterName]()) !== null)
            {
                return $filterReturnValue;
            }
        }

        return null;
    }
} 
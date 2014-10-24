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
    protected $container = null;
    /** @var IRouteCompiler The compiler used by this router */
    protected $compiler = null;
    /** @var Dispatcher The route dispatcher */
    protected $dispatcher = null;
    /** @var array The list of methods to their various routes */
    protected $routes = [
        HTTP\Request::METHOD_DELETE => [],
        HTTP\Request::METHOD_GET => [],
        HTTP\Request::METHOD_POST => [],
        HTTP\Request::METHOD_PUT => []
    ];
    /** @var array The list of options in the current group stack */
    protected $groupOptionsStack = [];

    /**
     * @param IoC\IContainer $container The IoC container
     * @param Dispatcher $dispatcher The route dispatcher
     * @param IRouteCompiler $compiler The route compiler
     */
    public function __construct(
        IoC\IContainer $container,
        Dispatcher $dispatcher,
        IRouteCompiler $compiler
    )
    {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->compiler = $compiler;
    }

    /**
     * Adds a route to the router
     *
     * @param Route $route The route to add
     */
    public function addRoute(Route $route)
    {
        $route = $this->applyGroupSettings($route);

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
     * Groups similar routes together so that you don't have to repeat route options
     *
     * @param array $options The list of options common to all routes added in the closure
     *      It can contain the following keys:
     *          "path" => The common path to be prepended to all the grouped routes,
     *          "pre" => The pre-filters to be added to all the grouped routes,
     *          "post" => The post-filters to be added to all the grouped routes
     * @param callable $closure A function that adds routes to the router
     */
    public function group(array $options, callable $closure)
    {
        array_push($this->groupOptionsStack, $options);
        call_user_func($closure);
        array_pop($this->groupOptionsStack);
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
     * @param HTTP\Request $request The request to route
     * @return HTTP\Response The response from the controller
     * @throws RouteException Thrown if the controller or method could not be called
     */
    public function route(HTTP\Request $request)
    {
        $method = $request->getMethod();

        /** @var Route $route */
        foreach($this->routes[$method] as $route)
        {
            $this->compiler->compile($route);
            $hostMatches = [];
            $pathMatches = [];

            if(
                preg_match($route->getHostRegex(), $request->getHeaders()->get("HOST"), $hostMatches) &&
                preg_match($route->getPathRegex(), $request->getPath(), $pathMatches)
            )
            {
                $mergedMatches = array_merge($hostMatches, $pathMatches);

                return $this->dispatcher->dispatch($route, $mergedMatches);
            }
        }

        // TODO: Implement a default controller
        return new HTTP\Response();
    }

    /**
     * Applies any group settings to a route
     *
     * @param Route $route The route to apply the settings to
     * @return Route The route with the applied settings
     */
    private function applyGroupSettings(Route $route)
    {
        $route->setRawPath($this->getGroupPath() . $route->getRawPath());
        $route->setRawHost($this->getGroupHost() . $route->getRawHost());
        $route->setControllerName($this->getGroupControllerNamespace() . $route->getControllerName());
        $groupPreFilters = $this->getGroupFilters("pre");
        $groupPostFilters = $this->getGroupFilters("post");

        if(count($groupPreFilters) > 0)
        {
            $route->addPreFilters($groupPreFilters, true);
        }

        if(count($groupPostFilters) > 0)
        {
            $route->addPostFilters($groupPostFilters, true);
        }

        return $route;
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
     * Gets the controller namespace from the current group stack
     *
     * @return string The controller namespace
     */
    private function getGroupControllerNamespace()
    {
        $controllerNamespace = "";

        foreach($this->groupOptionsStack as $groupOptions)
        {
            if(isset($groupOptions["controllerNamespace"]))
            {
                // Add trailing slashes if they're not there already
                if(substr($groupOptions["controllerNamespace"], -1) != "\\")
                {
                    $groupOptions["controllerNamespace"] .= "\\";
                }

                $controllerNamespace .= $groupOptions["controllerNamespace"];
            }
        }

        return $controllerNamespace;
    }

    /**
     * Gets the filters in the current group stack
     *
     * @param string $filterType The type of filter ("pre" or "post")
     * @return array The list of filters of all the groups
     */
    private function getGroupFilters($filterType)
    {
        $filters = [];

        foreach($this->groupOptionsStack as $groupOptions)
        {
            if(isset($groupOptions[$filterType]))
            {
                if(!is_array($groupOptions[$filterType]))
                {
                    $groupOptions[$filterType] = [$groupOptions[$filterType]];
                }

                $filters = array_merge($filters, $groupOptions[$filterType]);
            }
        }

        return $filters;
    }

    /**
     * Gets the host from the current group stack
     *
     * @return string The host
     */
    private function getGroupHost()
    {
        $host = "";

        foreach($this->groupOptionsStack as $groupOptions)
        {
            if(isset($groupOptions["host"]))
            {
                $host = $groupOptions["host"] . $host;
            }
        }

        return $host;
    }

    /**
     * Gets the path of the current group stack
     *
     * @return string The path of all the groups concatenated together
     */
    private function getGroupPath()
    {
        $path = "";

        foreach($this->groupOptionsStack as $groupOptions)
        {
            if(isset($groupOptions["path"]))
            {
                $path .= $groupOptions["path"];
            }
        }

        return $path;
    }
} 
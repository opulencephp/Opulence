<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a router for URL requests
 */
namespace RDev\Routing;
use RDev\HTTP;
use RDev\IoC;

class Router
{
    /** @var Compilers\ICompiler The compiler used by this router */
    protected $compiler = null;
    /** @var Dispatcher The route dispatcher */
    protected $dispatcher = null;
    /** @var Routes The list of routes */
    protected $routes = null;
    /** @var array The list of options in the current group stack */
    protected $groupOptionsStack = [];
    /** @var string The name of the controller class that will handle missing routes */
    protected $missedRouteControllerName = "";

    /**
     * @param Dispatcher $dispatcher The route dispatcher
     * @param Compilers\ICompiler $compiler The route compiler
     * @param string $missedRouteControllerName The name of the controller class that will handle missing routes
     */
    public function __construct(
        Dispatcher $dispatcher,
        Compilers\ICompiler $compiler,
        $missedRouteControllerName = "RDev\\Routing\\Controller"
    )
    {
        $this->dispatcher = $dispatcher;
        $this->compiler = $compiler;
        $this->routes = new Routes();
        $this->setMissedRouteControllerName($missedRouteControllerName);
    }

    /**
     * Adds a route to the router
     *
     * @param Route $route The route to add
     */
    public function addRoute(Route $route)
    {
        $route = $this->applyGroupSettings($route);
        $this->routes->add($route);
    }

    /**
     * Adds a route for the any method at the given path
     *
     * @param string $path The path to match on
     * @param array $options The list of options for this path
     */
    public function any($path, array $options)
    {
        $this->multiple($this->routes->getMethods(), $path, $options);
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
     * @return Routes
     */
    public function getRoutes()
    {
        return $this->routes;
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
     * Adds a route for the HEAD method at the given path
     *
     * @param string $path The path to match on
     * @param array $options The list of options for this path
     */
    public function head($path, array $options)
    {
        $route = $this->createRoute(HTTP\Request::METHOD_HEAD, $path, $options);
        $this->addRoute($route);
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
     * Adds a route for the OPTIONS method at the given path
     *
     * @param string $path The path to match on
     * @param array $options The list of options for this path
     */
    public function options($path, array $options)
    {
        $route = $this->createRoute(HTTP\Request::METHOD_OPTIONS, $path, $options);
        $this->addRoute($route);
    }

    /**
     * Adds a route for the PATCH method at the given path
     *
     * @param string $path The path to match on
     * @param array $options The list of options for this path
     */
    public function patch($path, array $options)
    {
        $route = $this->createRoute(HTTP\Request::METHOD_PATCH, $path, $options);
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
        foreach($this->routes->get($method) as $route)
        {
            $this->compiler->compile($route);
            $hostMatches = [];
            $pathMatches = [];

            if(
                (($route->isSecure() && $request->isSecure()) || !$route->isSecure()) &&
                preg_match($route->getHostRegex(), $request->getHeaders()->get("HOST"), $hostMatches) &&
                preg_match($route->getPathRegex(), $request->getPath(), $pathMatches)
            )
            {
                $mergedMatches = array_merge($hostMatches, $pathMatches);

                return $this->dispatcher->dispatch($route, $request, $mergedMatches);
            }
        }

        // If we've gotten here, we've got a missing route
        return $this->getMissingRouteResponse($request);
    }

    /**
     * @param IoC\IContainer $container
     */
    public function setIoCContainer($container)
    {
        $this->dispatcher->setIoCContainer($container);
    }

    /**
     * @param string $missedRouteControllerName
     */
    public function setMissedRouteControllerName($missedRouteControllerName)
    {
        $this->missedRouteControllerName = $missedRouteControllerName;
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
        $route->setSecure($this->isGroupSecure() || $route->isSecure());
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

    /**
     * Gets the response for a missing route
     *
     * @param HTTP\Request $request
     * @return HTTP\Response The response
     */
    private function getMissingRouteResponse(HTTP\Request $request)
    {
        return $this->dispatcher->dispatch(new MissingRoute($this->missedRouteControllerName), $request, []);
    }

    /**
     * Gets whether or not the current group stack is secure
     * If ANY of the groups were marked as HTTPS, then this will return true even if a sub-group is not marked HTTPS
     *
     * @return bool True if the group is secure, otherwise false
     */
    private function isGroupSecure()
    {
        foreach($this->groupOptionsStack as $groupOptions)
        {
            if(isset($groupOptions["https"]) && $groupOptions["https"])
            {
                return true;
            }
        }

        return false;
    }
} 
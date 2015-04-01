<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a router for URL requests
 */
namespace RDev\HTTP\Routing;
use InvalidArgumentException;
use RDev\HTTP\Requests\Request;
use RDev\HTTP\Responses\Response;
use RDev\HTTP\Routing\Compilers\ICompiler;
use RDev\HTTP\Routing\Dispatchers\IDispatcher;
use RDev\HTTP\Routing\Routes\MissingRoute;
use RDev\HTTP\Routing\Routes\Route;
use RDev\HTTP\Routing\Routes\RouteCollection;

class Router
{
    /** @var ICompiler The compiler used by this router */
    protected $compiler = null;
    /** @var IDispatcher The route dispatcher */
    protected $dispatcher = null;
    /** @var RouteCollection The list of routes */
    protected $routeCollection = null;
    /** @var Route|null The matched route if there is one, otherwise null */
    protected $matchedRoute = null;
    /** @var Controller|null The matched controller if there is one, otherwise null */
    protected $matchedController = null;
    /** @var array The list of options in the current group stack */
    protected $groupOptionsStack = [];
    /** @var string The name of the controller class that will handle missing routes */
    protected $missedRouteControllerName = "";

    /**
     * @param IDispatcher $dispatcher The route dispatcher
     * @param ICompiler $compiler The route compiler
     * @param string $missedRouteControllerName The name of the controller class that will handle missing routes
     * @throws InvalidArgumentException Thrown if the controller name does not exist
     */
    public function __construct(
        IDispatcher $dispatcher,
        ICompiler $compiler,
        $missedRouteControllerName = "RDev\\HTTP\\Routing\\Controller"
    )
    {
        $this->dispatcher = $dispatcher;
        $this->compiler = $compiler;
        $this->routeCollection = new RouteCollection();
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
        $this->routeCollection->add($route);
    }

    /**
     * Adds a route for the any method at the given path
     *
     * @param string $path The path to match on
     * @param array $options The list of options for this path
     */
    public function any($path, array $options)
    {
        $this->multiple($this->routeCollection->getMethods(), $path, $options);
    }

    /**
     * Adds a route for the DELETE method at the given path
     *
     * @param string $path The path to match on
     * @param array $options The list of options for this path
     */
    public function delete($path, array $options)
    {
        $route = $this->createRoute(Request::METHOD_DELETE, $path, $options);
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
        $route = $this->createRoute(Request::METHOD_GET, $path, $options);
        $this->addRoute($route);
    }

    /**
     * @return Controller|null
     */
    public function getMatchedController()
    {
        return $this->matchedController;
    }

    /**
     * @return Route|null
     */
    public function getMatchedRoute()
    {
        return $this->matchedRoute;
    }

    /**
     * Gets the reference to the list of routes
     *
     * @return RouteCollection
     */
    public function &getRouteCollection()
    {
        return $this->routeCollection;
    }

    /**
     * Groups similar routes together so that you don't have to repeat route options
     *
     * @param array $options The list of options common to all routes added in the closure
     *      It can contain the following keys:
     *          "path" => The common path to be prepended to all the grouped routes,
     *          "middleware" => The middleware to be added to all the grouped routes,
     *          "https" => Whether or not all the grouped routes are HTTPS,
     *          "variables" => The list of path variable regular expressions all the routes must match
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
        $route = $this->createRoute(Request::METHOD_HEAD, $path, $options);
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
        $route = $this->createRoute(Request::METHOD_OPTIONS, $path, $options);
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
        $route = $this->createRoute(Request::METHOD_PATCH, $path, $options);
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
        $route = $this->createRoute(Request::METHOD_POST, $path, $options);
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
        $route = $this->createRoute(Request::METHOD_PUT, $path, $options);
        $this->addRoute($route);
    }

    /**
     * Routes a request
     *
     * @param Request $request The request to route
     * @return Response The response from the controller
     * @throws RouteException Thrown if the controller or method could not be called
     */
    public function route(Request $request)
    {
        $method = $request->getMethod();

        /** @var Route $route */
        foreach($this->routeCollection->get($method) as $route)
        {
            $compiledRoute = $this->compiler->compile($route, $request);

            if($compiledRoute->isMatch())
            {
                $this->matchedRoute = $compiledRoute;

                return $this->dispatcher->dispatch($this->matchedRoute, $request, $this->matchedController);
            }
        }

        // If we've gotten here, we've got a missing route
        return $this->getMissingRouteResponse($request);
    }

    /**
     * @param string $missedRouteControllerName
     * @throws InvalidArgumentException Thrown if the controller name does not exist
     */
    public function setMissedRouteControllerName($missedRouteControllerName)
    {
        if(!class_exists($missedRouteControllerName))
        {
            throw new InvalidArgumentException(
                sprintf(
                    "Missed route controller class \"%s\" does not exist",
                    $missedRouteControllerName
                )
            );
        }

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
        // The route's variable regexes take precedence over group regexes
        $route->setVariableRegexes(array_merge($this->getVariableRegexes(), $route->getVariableRegexes()));
        $groupMiddleware = $this->getGroupMiddleware();

        if(count($groupMiddleware) > 0)
        {
            $route->addMiddleware($groupMiddleware, true);
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
                if(mb_substr($groupOptions["controllerNamespace"], -1) != "\\")
                {
                    $groupOptions["controllerNamespace"] .= "\\";
                }

                $controllerNamespace .= $groupOptions["controllerNamespace"];
            }
        }

        return $controllerNamespace;
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
     * Gets the middleware in the current group stack
     *
     * @return array The list of middleware of all the groups
     */
    private function getGroupMiddleware()
    {
        $middleware = [];

        foreach($this->groupOptionsStack as $groupOptions)
        {
            if(isset($groupOptions["middleware"]))
            {
                $middleware = array_merge($middleware, (array)$groupOptions["middleware"]);
            }
        }

        return $middleware;
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
     * @param Request $request
     * @return Response The response
     */
    private function getMissingRouteResponse(Request $request)
    {
        return $this->dispatcher->dispatch(
            new MissingRoute($this->missedRouteControllerName),
            $request,
            $this->matchedController
        );
    }

    /**
     * Gets the variable regexes from the current group stack
     *
     * @return array The The mapping of variable names to regexes
     */
    private function getVariableRegexes()
    {
        $variableRegexes = [];

        foreach($this->groupOptionsStack as $groupOptions)
        {
            if(isset($groupOptions["variables"]))
            {
                $variableRegexes = array_merge($variableRegexes, $groupOptions["variables"]);
            }
        }

        return $variableRegexes;
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
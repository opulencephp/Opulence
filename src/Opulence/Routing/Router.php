<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Routing;

use Opulence\Http\HttpException;
use Opulence\Http\Requests\Request;
use Opulence\Http\Requests\RequestMethods;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Dispatchers\IRouteDispatcher;
use Opulence\Routing\Routes\CompiledRoute;
use Opulence\Routing\Routes\Compilers\ICompiler;
use Opulence\Routing\Routes\Compilers\Parsers\IParser;
use Opulence\Routing\Routes\ParsedRoute;
use Opulence\Routing\Routes\Route;
use Opulence\Routing\Routes\RouteCollection;

/**
 * Defines a URL router
 */
class Router
{
    /** @var ICompiler The compiler used by this router */
    protected $compiler = null;
    /** @var IParser The parser used by this router */
    protected $parser = null;
    /** @var IRouteDispatcher The route dispatcher */
    protected $dispatcher = null;
    /** @var RouteCollection The list of routes */
    protected $routeCollection = null;
    /** @var CompiledRoute|null The matched route if there is one, otherwise null */
    protected $matchedRoute = null;
    /** @var Controller|mixed|null The matched controller if there is one, otherwise null */
    protected $matchedController = null;
    /** @var array The list of options in the current group stack */
    protected $groupOptionsStack = [];

    /**
     * @param IRouteDispatcher $dispatcher The route dispatcher
     * @param ICompiler $compiler The route compiler
     * @param IParser $parser The route parser
     */
    public function __construct(
        IRouteDispatcher $dispatcher,
        ICompiler $compiler,
        IParser $parser
    ) {
        $this->dispatcher = $dispatcher;
        $this->compiler = $compiler;
        $this->parser = $parser;
        $this->routeCollection = new RouteCollection();
    }

    /**
     * Adds a route to the router
     *
     * @param Route $route The route to add
     * @return ParsedRoute The route with the group settings applied
     */
    public function addRoute(Route $route) : ParsedRoute
    {
        $route = $this->applyGroupSettings($route);
        $parsedRoute = $this->parser->parse($route);
        $this->routeCollection->add($parsedRoute);

        return $parsedRoute;
    }

    /**
     * Adds a route for the any method at the given path
     *
     * @param string $path The path to match on
     * @param string|callable $controller The name of the controller/method or the callback
     * @param array $options The list of options for this path
     * @return ParsedRoute[] The list of generated routes
     */
    public function any(string $path, $controller, array $options = []) : array
    {
        return $this->multiple(RouteCollection::getMethods(), $path, $controller, $options);
    }

    /**
     * Adds a route for the DELETE method at the given path
     *
     * @param string $path The path to match on
     * @param string|callable $controller The name of the controller/method or the callback
     * @param array $options The list of options for this path
     * @return ParsedRoute The generated route
     */
    public function delete(string $path, $controller, array $options = []) : ParsedRoute
    {
        $route = $this->createRoute(RequestMethods::DELETE, $path, $controller, $options);

        return $this->addRoute($route);
    }

    /**
     * Adds a route for the GET method at the given path
     *
     * @param string $path The path to match on
     * @param string|callable $controller The name of the controller/method or the callback
     * @param array $options The list of options for this path
     * @return ParsedRoute The generated route
     */
    public function get(string $path, $controller, array $options = []) : ParsedRoute
    {
        $route = $this->createRoute(RequestMethods::GET, $path, $controller, $options);

        return $this->addRoute($route);
    }

    /**
     * @return Controller|mixed|null
     */
    public function getMatchedController()
    {
        return $this->matchedController;
    }

    /**
     * @return CompiledRoute|null
     */
    public function getMatchedRoute()
    {
        return $this->matchedRoute;
    }

    /**
     * Gets the reference to the list of routes
     *
     * @return RouteCollection The route collection
     */
    public function &getRouteCollection() : RouteCollection
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
     *          "vars" => The list of path variable regular expressions all the routes must match
     * @param callable $callback A function that adds routes to the router
     */
    public function group(array $options, callable $callback)
    {
        array_push($this->groupOptionsStack, $options);
        $callback($this);
        array_pop($this->groupOptionsStack);
    }

    /**
     * Adds a route for the HEAD method at the given path
     *
     * @param string $path The path to match on
     * @param string|callable $controller The name of the controller/method or the callback
     * @param array $options The list of options for this path
     * @return ParsedRoute The generated route
     */
    public function head(string $path, $controller, array $options = []) : ParsedRoute
    {
        $route = $this->createRoute(RequestMethods::HEAD, $path, $controller, $options);

        return $this->addRoute($route);
    }

    /**
     * Adds a route for multiple methods
     *
     * @param array $methods The list of methods to match on
     * @param string $path The path to match on
     * @param string|callable $controller The name of the controller/method or the callback
     * @param array $options The list of options for this path
     * @return ParsedRoute[] The list of routes generated
     */
    public function multiple(array $methods, string $path, $controller, array $options = []) : array
    {
        $routes = [];

        foreach ($methods as $method) {
            $route = $this->createRoute($method, $path, $controller, $options);
            $parsedRoute = $this->addRoute($route);
            $routes[] = $parsedRoute;
        }

        return $routes;
    }

    /**
     * Adds a route for the OPTIONS method at the given path
     *
     * @param string $path The path to match on
     * @param string|callable $controller The name of the controller/method or the callback
     * @param array $options The list of options for this path
     * @return ParsedRoute The generated route
     */
    public function options(string $path, $controller, array $options = []) : ParsedRoute
    {
        $route = $this->createRoute(RequestMethods::OPTIONS, $path, $controller, $options);

        return $this->addRoute($route);
    }

    /**
     * Adds a route for the PATCH method at the given path
     *
     * @param string $path The path to match on
     * @param string|callable $controller The name of the controller/method or the callback
     * @param array $options The list of options for this path
     * @return ParsedRoute The generated route
     */
    public function patch(string $path, $controller, array $options = []) : ParsedRoute
    {
        $route = $this->createRoute(RequestMethods::PATCH, $path, $controller, $options);

        return $this->addRoute($route);
    }

    /**
     * Adds a route for the POST method at the given path
     *
     * @param string $path The path to match on
     * @param string|callable $controller The name of the controller/method or the callback
     * @param array $options The list of options for this path
     * @return ParsedRoute The generated route
     */
    public function post(string $path, $controller, array $options = []) : ParsedRoute
    {
        $route = $this->createRoute(RequestMethods::POST, $path, $controller, $options);

        return $this->addRoute($route);
    }

    /**
     * Adds a route for the PUT method at the given path
     *
     * @param string $path The path to match on
     * @param string|callable $controller The name of the controller/method or the callback
     * @param array $options The list of options for this path
     * @return ParsedRoute The generated route
     */
    public function put(string $path, $controller, array $options = []) : ParsedRoute
    {
        $route = $this->createRoute(RequestMethods::PUT, $path, $controller, $options);

        return $this->addRoute($route);
    }

    /**
     * Routes a request
     *
     * @param Request $request The request to route
     * @return Response The response from the controller
     * @throws RouteException Thrown if the controller or method could not be called
     * @throws HttpException Thrown if there was no matching route
     */
    public function route(Request $request) : Response
    {
        $method = $request->getMethod();

        /** @var ParsedRoute $route */
        foreach ($this->routeCollection->get($method) as $route) {
            $compiledRoute = $this->compiler->compile($route, $request);

            if ($compiledRoute->isMatch()) {
                $this->matchedRoute = $compiledRoute;

                return $this->dispatcher->dispatch($this->matchedRoute, $request, $this->matchedController);
            }
        }

        // If we've gotten here, we've got a missing route
        throw new HttpException(404);
    }

    /**
     * @param RouteCollection $routeCollection
     */
    public function setRouteCollection(RouteCollection $routeCollection)
    {
        $this->routeCollection = $routeCollection;
    }

    /**
     * Applies any group settings to a route
     *
     * @param Route $route The route to apply the settings to
     * @return Route The route with the applied settings
     */
    private function applyGroupSettings(Route $route) : Route
    {
        $route->setRawPath($this->getGroupPath() . $route->getRawPath());
        $route->setRawHost($this->getGroupHost() . $route->getRawHost());

        if (!$route->usesCallable()) {
            $route->setControllerName($this->getGroupControllerNamespace() . $route->getControllerName());
        }

        $route->setSecure($this->groupIsSecure() || $route->isSecure());
        // The route's variable regexes take precedence over group regexes
        $route->setVarRegexes(array_merge($this->getVarRegexes(), $route->getVarRegexes()));
        $groupMiddleware = $this->getGroupMiddleware();

        if (count($groupMiddleware) > 0) {
            $route->addMiddleware($groupMiddleware, true);
        }

        return $route;
    }

    /**
     * Creates a route from the input
     *
     * @param string $method The method whose route this is
     * @param string $path The path to match on
     * @param string|callable $controller The name of the controller/method or the callback
     * @param array $options The list of options for this path
     * @return Route The route from the input
     */
    private function createRoute(string $method, string $path, $controller, array $options = []) : Route
    {
        return new Route([$method], $path, $controller, $options);
    }

    /**
     * Gets the controller namespace from the current group stack
     *
     * @return string The controller namespace
     */
    private function getGroupControllerNamespace() : string
    {
        $controllerNamespace = '';

        foreach ($this->groupOptionsStack as $groupOptions) {
            if (isset($groupOptions['controllerNamespace'])) {
                // Add trailing slashes if they're not there already
                if (mb_substr($groupOptions['controllerNamespace'], -1) !== '\\') {
                    $groupOptions['controllerNamespace'] .= '\\';
                }

                $controllerNamespace .= $groupOptions['controllerNamespace'];
            }
        }

        return $controllerNamespace;
    }

    /**
     * Gets the host from the current group stack
     *
     * @return string The host
     */
    private function getGroupHost() : string
    {
        $host = '';

        foreach ($this->groupOptionsStack as $groupOptions) {
            if (isset($groupOptions['host'])) {
                $host = $groupOptions['host'] . $host;
            }
        }

        return $host;
    }

    /**
     * Gets the middleware in the current group stack
     *
     * @return array The list of middleware of all the groups
     */
    private function getGroupMiddleware() : array
    {
        $middleware = [];

        foreach ($this->groupOptionsStack as $groupOptions) {
            if (isset($groupOptions['middleware'])) {
                if (!is_array($groupOptions['middleware'])) {
                    $groupOptions['middleware'] = [$groupOptions['middleware']];
                }

                $middleware = array_merge($middleware, $groupOptions['middleware']);
            }
        }

        return $middleware;
    }

    /**
     * Gets the path of the current group stack
     *
     * @return string The path of all the groups concatenated together
     */
    private function getGroupPath() : string
    {
        $path = '';

        foreach ($this->groupOptionsStack as $groupOptions) {
            if (isset($groupOptions['path'])) {
                $path .= $groupOptions['path'];
            }
        }

        return $path;
    }

    /**
     * Gets the variable regexes from the current group stack
     *
     * @return array The The mapping of variable names to regexes
     */
    private function getVarRegexes() : array
    {
        $variableRegexes = [];

        foreach ($this->groupOptionsStack as $groupOptions) {
            if (isset($groupOptions['vars'])) {
                $variableRegexes = array_merge($variableRegexes, $groupOptions['vars']);
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
    private function groupIsSecure() : bool
    {
        foreach ($this->groupOptionsStack as $groupOptions) {
            if (isset($groupOptions['https']) && $groupOptions['https']) {
                return true;
            }
        }

        return false;
    }
}

<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Routing\Routes;

use InvalidArgumentException;
use RuntimeException;

/**
 * Defines an individual route
 */
class Route
{
    /** @var string The HTTP method for this route */
    protected $methods = [];
    /** @var string The raw path passed into the route */
    protected $rawPath = '';
    /** @var string The raw host passed into the route */
    protected $rawHost = '';
    /** @var string The name of the controller this routes to */
    protected $controllerName = '';
    /** @var string The name of the controller method this route calls */
    protected $controllerMethod = '';
    /** @var string|callable|null The controller/method or closure to be used as a controller */
    protected $controller = null;
    /** @var string The name of this route, if it is a named route */
    protected $name = '';
    /** @var bool Whether or not this route only matches HTTPS requests */
    protected $isSecure = false;
    /** @var bool Whether or not this route uses a callable as a controller */
    protected $usesCallable = false;
    /** @var array The mapping of route variable names to their regexes */
    protected $varRegexes = [];
    /** @var array The list of middleware to run when dispatching this route */
    protected $middleware = [];

    /**
     * @param string|array $methods The HTTP method or list of methods this route matches on
     * @param string $rawPath The raw path to match on
     * @param string|callable $controller The name of the controller/method or the callback
     * @param array $options The list of options
     * @throws RuntimeException Thrown if there is no controller specified in the options
     * @throws InvalidArgumentException Thrown if the controller name/method is incorrectly formatted
     */
    public function __construct($methods, string $rawPath, $controller, array $options = [])
    {
        $this->methods = (array)$methods;
        $this->rawPath = $rawPath;

        $this->setControllerVars($controller);

        if (isset($options['vars'])) {
            $this->setVarRegexes($options['vars']);
        }

        if (isset($options['middleware'])) {
            $this->addMiddleware($options['middleware']);
        }

        if (isset($options['host'])) {
            $this->setRawHost($options['host']);
        }

        if (isset($options['name'])) {
            $this->setName($options['name']);
        }

        if (isset($options['https'])) {
            $this->setSecure($options['https']);
        }
    }

    /**
     * Adds middleware to this route
     *
     * @param string|array $middleware The middleware or list of middleware to add
     * @param bool $prepend True if we want to prepend the middleware (give them higher priority), otherwise false
     */
    public function addMiddleware($middleware, bool $prepend = false) : void
    {
        if (!is_array($middleware)) {
            $middleware = [$middleware];
        }

        if ($prepend) {
            $this->middleware = array_merge($middleware, $this->middleware);
        } else {
            $this->middleware = array_merge($this->middleware, $middleware);
        }

        $this->middleware = array_unique($this->middleware, SORT_REGULAR);
    }

    /**
     * @return callable|string|null
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getControllerMethod() : string
    {
        return $this->controllerMethod;
    }

    /**
     * @return string
     */
    public function getControllerName() : string
    {
        return $this->controllerName;
    }

    /**
     * @return array
     */
    public function getMethods() : array
    {
        return $this->methods;
    }

    /**
     * @return array
     */
    public function getMiddleware() : array
    {
        return $this->middleware;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRawHost() : string
    {
        return $this->rawHost;
    }

    /**
     * @return string
     */
    public function getRawPath() : string
    {
        return $this->rawPath;
    }

    /**
     * Gets the regex for a path parameter
     *
     * @param string $name The name of the parameter whose regex we want
     * @return string|null The regex for the parameter if there are any, otherwise null
     */
    public function getVarRegex(string $name) : ?string
    {
        return $this->varRegexes[$name] ?? null;
    }

    /**
     * Gets the regexes for path parameters
     *
     * @return array The mapping of variable names to regexes
     */
    public function getVarRegexes() : array
    {
        return $this->varRegexes;
    }

    /**
     * @return bool
     */
    public function isSecure() : bool
    {
        return $this->isSecure;
    }

    /**
     * @param callable|string $controller The callable controller (or the serialized callable if the route is cached)
     */
    public function setControllerCallable($controller) : void
    {
        $this->usesCallable = true;
        $this->controller = $controller;
    }

    /**
     * @param string $controllerMethod
     */
    public function setControllerMethod(string $controllerMethod) : void
    {
        $this->controllerMethod = $controllerMethod;
        $this->controller = "{$this->controllerName}@{$this->controllerMethod}";
    }

    /**
     * @param string $controllerName
     */
    public function setControllerName(string $controllerName) : void
    {
        $this->controllerName = $controllerName;
        $this->controller = "{$this->controllerName}@{$this->controllerMethod}";
    }

    /**
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    /**
     * @param string $rawHost
     */
    public function setRawHost(string $rawHost) : void
    {
        $this->rawHost = $rawHost;
    }

    /**
     * @param string $rawPath
     */
    public function setRawPath(string $rawPath) : void
    {
        $this->rawPath = $rawPath;
    }

    /**
     * @param bool $isSecure
     */
    public function setSecure(bool $isSecure) : void
    {
        $this->isSecure = $isSecure;
    }

    /**
     * Sets the regex a variable must satisfy
     *
     * @param string $name The name of the variable whose regex we're setting
     * @param string $regex The regex to set
     */
    public function setVarRegex(string $name, string $regex) : void
    {
        $this->varRegexes[$name] = $regex;
    }

    /**
     * Sets regexes variables must satisfy
     *
     * @param array $regexes The mapping of variable names to their regexes
     */
    public function setVarRegexes(array $regexes) : void
    {
        foreach ($regexes as $varName => $regex) {
            $this->setVarRegex($varName, $regex);
        }
    }

    /**
     * @return bool
     */
    public function usesCallable() : bool
    {
        return $this->usesCallable;
    }

    /**
     * Sets the controller name and method from the raw string
     *
     * @param string|callable $controller The name of the controller/method or the callback
     * @throws InvalidArgumentException Thrown if the controller string is not formatted correctly
     */
    protected function setControllerVars($controller) : void
    {
        $this->controller = $controller;

        if (is_callable($controller)) {
            $this->setControllerCallable($controller);
        } else {
            $this->usesCallable = false;
            $atCharPos = strpos($controller, '@');

            // Make sure the "@" is somewhere in the middle of the string
            if ($atCharPos === false || $atCharPos === 0 || $atCharPos === mb_strlen($controller) - 1) {
                throw new InvalidArgumentException('Controller string is not formatted correctly');
            }

            $this->controllerName = substr($controller, 0, $atCharPos);
            $this->controllerMethod = substr($controller, $atCharPos + 1);
        }
    }
}

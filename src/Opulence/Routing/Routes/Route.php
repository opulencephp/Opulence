<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Routing\Routes;

use Closure;
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
    protected $rawPath = "";
    /** @var string The raw host passed into the route */
    protected $rawHost = "";
    /** @var string The name of the controller this routes to */
    protected $controllerName = "";
    /** @var string The name of the controller method this route calls */
    protected $controllerMethod = "";
    /** @var string|Closure|null The controller/method or closure to be used as a controller */
    protected $controller = null;
    /** @var string The name of this route, if it is a named route */
    protected $name = "";
    /** @var bool Whether or not this route only matches HTTPS requests */
    protected $isSecure = false;
    /** @var bool Whether or not this route uses a closure as a controller */
    protected $usesClosure = false;
    /** @var array The mapping of route variable names to their regexes */
    protected $varRegexes = [];
    /** @var array The list of middleware to run when dispatching this route */
    protected $middleware = [];

    /**
     * @param string|array $methods The HTTP method or list of methods this route matches on
     * @param string $rawPath The raw path to match on
     * @param string|Closure $controller The name of the controller/method or the callback
     * @param array $options The list of options
     * @throws RuntimeException Thrown if there is no controller specified in the options
     * @throws InvalidArgumentException Thrown if the controller name/method is incorrectly formatted
     */
    public function __construct($methods, $rawPath, $controller, array $options = [])
    {
        $this->methods = (array)$methods;
        $this->rawPath = $rawPath;

        $this->setControllerVars($controller);

        if (isset($options["vars"])) {
            $this->setVarRegexes($options["vars"]);
        }

        if (isset($options["middleware"])) {
            $this->addMiddleware($options["middleware"]);
        }

        if (isset($options["host"])) {
            $this->setRawHost($options["host"]);
        }

        if (isset($options["name"])) {
            $this->setName($options["name"]);
        }

        if (isset($options["https"])) {
            $this->setSecure($options["https"]);
        }
    }

    /**
     * Adds pre-filters to this route
     *
     * @param string|array $filters The filter or list of pre-filters to add
     * @param bool $prepend True if we want to prepend the filters (give them higher priority), otherwise false
     */
    public function addMiddleware($filters, $prepend = false)
    {
        $filters = (array)$filters;

        if ($prepend) {
            $this->middleware = array_merge($filters, $this->middleware);
        } else {
            $this->middleware = array_merge($this->middleware, $filters);
        }

        $this->middleware = array_unique($this->middleware);
    }

    /**
     * @return Closure|string|null
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getControllerMethod()
    {
        return $this->controllerMethod;
    }

    /**
     * @return string
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @return array
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRawHost()
    {
        return $this->rawHost;
    }

    /**
     * @return string
     */
    public function getRawPath()
    {
        return $this->rawPath;
    }

    /**
     * Gets the regex for a path parameter
     *
     * @param string $name The name of the parameter whose regex we want
     * @return string|null The regex for the parameter if there are any, otherwise null
     */
    public function getVarRegex($name)
    {
        return isset($this->varRegexes[$name]) ? $this->varRegexes[$name] : null;
    }

    /**
     * Gets the regexes for path parameters
     *
     * @return array The mapping of variable names to regexes
     */
    public function getVarRegexes()
    {
        return $this->varRegexes;
    }

    /**
     * @return bool
     */
    public function isSecure()
    {
        return $this->isSecure;
    }

    /**
     * @param Closure $controller
     */
    public function setControllerClosure($controller)
    {
        $this->usesClosure = true;
        $this->controller = $controller;
    }

    /**
     * @param string $controllerMethod
     */
    public function setControllerMethod($controllerMethod)
    {
        $this->controllerMethod = $controllerMethod;
        $this->controller = "{$this->controllerName}@{$this->controllerMethod}";
    }

    /**
     * @param string $controllerName
     */
    public function setControllerName($controllerName)
    {
        $this->controllerName = $controllerName;
        $this->controller = "{$this->controllerName}@{$this->controllerMethod}";
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $rawHost
     */
    public function setRawHost($rawHost)
    {
        $this->rawHost = $rawHost;
    }

    /**
     * @param string $rawPath
     */
    public function setRawPath($rawPath)
    {
        $this->rawPath = $rawPath;
    }

    /**
     * @param bool $isSecure
     */
    public function setSecure($isSecure)
    {
        $this->isSecure = (bool)$isSecure;
    }

    /**
     * Sets the regex a variable must satisfy
     *
     * @param string $name The name of the variable whose regex we're setting
     * @param string $regex The regex to set
     */
    public function setVarRegex($name, $regex)
    {
        $this->varRegexes[$name] = $regex;
    }

    /**
     * Sets regexes variables must satisfy
     *
     * @param array $regexes The mapping of variable names to their regexes
     */
    public function setVarRegexes(array $regexes)
    {
        foreach ($regexes as $varName => $regex) {
            $this->setVarRegex($varName, $regex);
        }
    }

    /**
     * @return bool
     */
    public function usesClosure()
    {
        return $this->usesClosure;
    }

    /**
     * Sets the controller name and method from the raw string
     *
     * @param string|Closure $controller The name of the controller/method or the callback
     * @throws InvalidArgumentException Thrown if the controller string is not formatted correctly
     */
    protected function setControllerVars($controller)
    {
        $this->controller = $controller;

        if (is_callable($controller)) {
            $this->setControllerClosure($controller);
        } else {
            $this->usesClosure = false;
            $atCharPos = strpos($controller, "@");

            // Make sure the "@" is somewhere in the middle of the string
            if ($atCharPos === false || $atCharPos === 0 || $atCharPos === mb_strlen($controller) - 1) {
                throw new InvalidArgumentException("Controller string is not formatted correctly");
            }

            $this->controllerName = substr($controller, 0, $atCharPos);
            $this->controllerMethod = substr($controller, $atCharPos + 1);
        }
    }
} 
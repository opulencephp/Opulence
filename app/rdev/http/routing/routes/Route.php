<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines an individual route
 */
namespace RDev\HTTP\Routing\Routes;
use InvalidArgumentException;
use RuntimeException;

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
    /** @var string The name of this route, if it is a named route */
    protected $name = "";
    /** @var bool Whether or not this route only matches HTTPS requests */
    protected $isSecure = false;
    /** @var array The mapping of route variable names to their regexes */
    protected $variableRegexes = [];
    /** @var array The list of middleware to run when dispatching this route */
    protected $middleware = [];

    /**
     * @param string|array $methods The HTTP method or list of methods this route matches on
     * @param string $rawPath The raw path to match on
     * @param array $options The list of options
     * @throws RuntimeException Thrown if there is no controller specified in the options
     * @throws InvalidArgumentException Thrown if the controller name/method is incorrectly formatted
     */
    public function __construct($methods, $rawPath, array $options)
    {
        $this->methods = (array)$methods;
        $this->rawPath = $rawPath;

        if(!isset($options["controller"]))
        {
            throw new RuntimeException("No controller specified for route");
        }

        $this->setControllerVariables($options["controller"]);

        if(isset($options["variables"]))
        {
            $this->setVariableRegexes($options["variables"]);
        }

        if(isset($options["middleware"]))
        {
            $this->addMiddleware($options["middleware"]);
        }

        if(isset($options["host"]))
        {
            $this->setRawHost($options["host"]);
        }

        if(isset($options["name"]))
        {
            $this->setName($options["name"]);
        }

        if(isset($options["https"]))
        {
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

        if($prepend)
        {
            $this->middleware = array_merge($filters, $this->middleware);
        }
        else
        {
            $this->middleware = array_merge($this->middleware, $filters);
        }

        $this->middleware = array_unique($this->middleware);
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
    public function getVariableRegex($name)
    {
        return isset($this->variableRegexes[$name]) ? $this->variableRegexes[$name] : null;
    }

    /**
     * Gets the regexes for path parameters
     *
     * @return array The mapping of variable names to regexes
     */
    public function getVariableRegexes()
    {
        return $this->variableRegexes;
    }

    /**
     * @return boolean
     */
    public function isSecure()
    {
        return $this->isSecure;
    }

    /**
     * @param string $controllerMethod
     */
    public function setControllerMethod($controllerMethod)
    {
        $this->controllerMethod = $controllerMethod;
    }

    /**
     * @param string $controllerName
     */
    public function setControllerName($controllerName)
    {
        $this->controllerName = $controllerName;
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
    public function setVariableRegex($name, $regex)
    {
        $this->variableRegexes[$name] = $regex;
    }

    /**
     * Sets regexes variables must satisfy
     *
     * @param array $regexes The mapping of variable names to their regexes
     */
    public function setVariableRegexes(array $regexes)
    {
        foreach($regexes as $variableName => $regex)
        {
            $this->setVariableRegex($variableName, $regex);
        }
    }

    /**
     * Sets the controller name and method from the raw string
     *
     * @param string $controllerString The string to set the variables from
     * @throws InvalidArgumentException Thrown if the controller string is not formatted correctly
     */
    protected function setControllerVariables($controllerString)
    {
        $atCharPos = strpos($controllerString, "@");

        // Make sure the "@" is somewhere in the middle of the string
        if($atCharPos === false || $atCharPos === 0 || $atCharPos === mb_strlen($controllerString) - 1)
        {
            throw new InvalidArgumentException("Controller string is not formatted correctly");
        }

        $this->controllerName = substr($controllerString, 0, $atCharPos);
        $this->controllerMethod = substr($controllerString, $atCharPos + 1);
    }
} 
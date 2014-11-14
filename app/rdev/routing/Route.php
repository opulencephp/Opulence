<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an individual route
 */
namespace RDev\Routing;

class Route
{
    /** @var string The HTTP method for this route */
    private $methods = [];
    /** @var string The raw path passed into the route */
    private $rawPath = "";
    /** @var string The compiled (regex) path */
    private $pathRegex = "";
    /** @var string The raw host passed into the route */
    private $rawHost = "";
    /** @var string|null The compiled (regex) host if set, otherwise null */
    private $hostRegex = null;
    /** @var string The name of the controller this routes to */
    private $controllerName = "";
    /** @var string The name of the controller method this route calls */
    private $controllerMethod = "";
    /** @var string The name of this route, if it is a named route */
    private $name = "";
    /** @var bool Whether or not this route only matches HTTPS requests */
    private $isSecure = false;
    /** @var array The mapping of route variable names to their regexes */
    private $variableRegexes = [];
    /** @var array The mapping of route-variables to their default values */
    private $defaultValues = [];
    /** @var array The list of filters to run before dispatching a route */
    private $preFilters = [];
    /** @var array The list of filters to run after dispatching a route */
    private $postFilters = [];

    /**
     * @param string|array $methods The HTTP method or list of methods this route matches on
     * @param string $rawPath The raw path to match on
     * @param array $options The list of options
     * @throws \RuntimeException Thrown if there is no controller specified in the options
     * @throws \InvalidArgumentException Thrown if the controller name/method is incorrectly formatted
     */
    public function __construct($methods, $rawPath, array $options)
    {
        if(!is_array($methods))
        {
            $methods = [$methods];
        }

        $this->methods = $methods;
        $this->rawPath = $rawPath;

        if(!isset($options["controller"]))
        {
            throw new \RuntimeException("No controller specified for route");
        }

        $this->setControllerVariables($options["controller"]);

        if(isset($options["variables"]))
        {
            $this->setVariableRegexes($options["variables"]);
        }

        if(isset($options["pre"]))
        {
            $this->addPreFilters($options["pre"]);
        }

        if(isset($options["post"]))
        {
            $this->addPostFilters($options["post"]);
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
     * Adds post-filters to this route
     *
     * @param string|array $filters The filter or list of post-filters to add
     * @param bool $prepend True if we want to prepend the filters (give them higher priority), otherwise false
     */
    public function addPostFilters($filters, $prepend = false)
    {
        if(!is_array($filters))
        {
            $filters = [$filters];
        }

        if($prepend)
        {
            $this->postFilters = array_merge($filters, $this->postFilters);
        }
        else
        {
            $this->postFilters = array_merge($this->postFilters, $filters);
        }

        $this->postFilters = array_unique($this->postFilters);
    }

    /**
     * Adds pre-filters to this route
     *
     * @param string|array $filters The filter or list of pre-filters to add
     * @param bool $prepend True if we want to prepend the filters (give them higher priority), otherwise false
     */
    public function addPreFilters($filters, $prepend = false)
    {
        if(!is_array($filters))
        {
            $filters = [$filters];
        }

        if($prepend)
        {
            $this->preFilters = array_merge($filters, $this->preFilters);
        }
        else
        {
            $this->preFilters = array_merge($this->preFilters, $filters);
        }

        $this->preFilters = array_unique($this->preFilters);
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
     * Gets the default value for a variable
     *
     * @param string $variableName The name of the variable whose default value we want
     * @return mixed|null The default value for the variable if it exists, otherwise null
     */
    public function getDefaultValue($variableName)
    {
        if(isset($this->defaultValues[$variableName]))
        {
            return $this->defaultValues[$variableName];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getHostRegex()
    {
        // Default to matching everything if it isn't set
        return is_null($this->hostRegex) ? "/^.*$/" : $this->hostRegex;
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
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
    public function getPathRegex()
    {
        return $this->pathRegex;
    }

    /**
     * @return array
     */
    public function getPostFilters()
    {
        return $this->postFilters;
    }

    /**
     * @return array
     */
    public function getPreFilters()
    {
        return $this->preFilters;
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
     * Sets a default value for a variable
     *
     * @param string $variableName The name of the variable whose default value we're setting
     * @param mixed $defaultValue The default value for the variable
     */
    public function setDefaultValue($variableName, $defaultValue)
    {
        $this->defaultValues[$variableName] = $defaultValue;
    }

    /**
     * @param string $hostRegex
     */
    public function setHostRegex($hostRegex)
    {
        $this->hostRegex = $hostRegex;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $regex
     */
    public function setPathRegex($regex)
    {
        $this->pathRegex = $regex;
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
     * Sets the controller name and method from the raw string
     *
     * @param string $controllerString The string to set the variables from
     * @throws \InvalidArgumentException Thrown if the controller string is not formatted correctly
     */
    private function setControllerVariables($controllerString)
    {
        $atCharPos = strpos($controllerString, "@");

        // Make sure the "@" is somewhere in the middle of the string
        if($atCharPos === false || $atCharPos === 0 || $atCharPos === strlen($controllerString) - 1)
        {
            throw new \InvalidArgumentException("Controller string is not formatted correctly");
        }

        $this->controllerName = substr($controllerString, 0, $atCharPos);
        $this->controllerMethod = substr($controllerString, $atCharPos + 1);
    }

    /**
     * Sets route variable regexes
     *
     * @param array $variableRegexes The mapping of variable names to their regexes
     */
    private function setVariableRegexes(array $variableRegexes)
    {
        foreach($variableRegexes as $variableName => $regex)
        {
            $this->setVariableRegex($variableName, $regex);
        }
    }
} 
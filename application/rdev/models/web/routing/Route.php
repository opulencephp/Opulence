<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an individual route
 */
namespace RDev\Models\Web\Routing;

class Route
{
    /** @var string The HTTP method for this route */
    private $methods = [];
    /** @var string The raw path passed into the route */
    private $rawPath = "";
    /** @var string The compiled (regex) path */
    private $regex = "";
    /** @var string The name of the controller this routes to */
    private $controllerName = "";
    /** @var string The name of the controller method this route calls */
    private $controllerMethod = "";
    /** @var array The mapping of route variable names to their regexes */
    private $variableRegexes = [];
    /** @var array The mapping of route-variables to their default values */
    private $defaultValues = [];
    /** @var array The list of filters to run before dispatching a route */
    private $beforeFilters = [];
    /** @var array The list of filters to run after dispatching a route */
    private $afterFilters = [];

    /**
     * @param array $methods The HTTP methods this route matches on
     * @param string $path The raw path to match on
     * @param array $options The list of options
     * @throws \RuntimeException Thrown if there is no controller specified in the options
     * @throws \InvalidArgumentException Thrown if the controller name/method is incorrectly formatted
     */
    public function __construct(array $methods, $path, array $options)
    {
        $this->methods = $methods;
        $this->rawPath = $path;

        if(!isset($options["controller"]))
        {
            throw new \RuntimeException("No controller specified for route");
        }

        $this->setControllerVariables($options["controller"]);

        if(isset($options["variables"]))
        {
            $this->setVariableRegexes($options["variables"]);
        }

        if(isset($options["before"]))
        {
            $this->setBeforeFilters($options["before"]);
        }

        if(isset($options["after"]))
        {
            $this->setAfterFilters($options["after"]);
        }
    }

    /**
     * @return array
     */
    public function getAfterFilters()
    {
        return $this->afterFilters;
    }

    /**
     * @return array
     */
    public function getBeforeFilters()
    {
        return $this->beforeFilters;
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
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @return string
     */
    public function getRawPath()
    {
        return $this->rawPath;
    }

    /**
     * @return string
     */
    public function getRegex()
    {
        return $this->regex;
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
     * @param string $regex
     */
    public function setRegex($regex)
    {
        $this->regex = $regex;
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
     * Sets the filters to run after dispatching a route
     *
     * @param string|array $filters The filter or list of filters to run after dispatching a route
     */
    private function setAfterFilters($filters)
    {
        if(!is_array($filters))
        {
            $filters = [$filters];
        }

        $this->afterFilters = $filters;
    }

    /**
     * Sets the filters to run before dispatching a route
     *
     * @param string|array $filters The filter or list of filters to run before dispatching a route
     */
    private function setBeforeFilters($filters)
    {
        if(!is_array($filters))
        {
            $filters = [$filters];
        }

        $this->beforeFilters = $filters;
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
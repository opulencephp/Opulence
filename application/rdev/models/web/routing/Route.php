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
    /** @var array The list of options for this route */
    private $options = [];
    /** @var array The mapping of route-variables to their default values */
    private $defaultValues = [];

    /**
     * @param array $methods The HTTP methods this route matches on
     * @param string $path The raw path to match on
     * @param array $options The list of options
     */
    public function __construct(array $methods, $path, array $options)
    {
        $this->methods = $methods;
        $this->rawPath = $path;
        $this->options = $options;
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
        if(isset($this->options["variables"][$name]))
        {
            return $this->options["variables"][$name];
        }

        return null;
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
        if(!isset($this->options["variables"]))
        {
            $this->options["variables"] = [];
        }

        $this->options["variables"][$name] = $regex;
    }
} 
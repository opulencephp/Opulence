<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Defines a parsed route
 * This is different than a compiled route because this does not have the context of a particular request
 */
namespace RDev\Routing\Routes;

class ParsedRoute extends Route
{
    /** @var string|null The parsed (regex) host if set, otherwise null */
    protected $hostRegex = null;
    /** @var string The parsed (regex) path */
    protected $pathRegex = "";
    /** @var array The mapping of route-variables to their default values */
    protected $defaultValues = [];

    /**
     * @param Route $route The unparsed route whose properties we are copying
     */
    public function __construct(Route $route)
    {
        $this->methods = $route->getMethods();
        $this->setControllerName($route->getControllerName());
        $this->setControllerMethod($route->getControllerMethod());
        $this->setName($route->getName());
        $this->setRawHost($route->getRawHost());
        $this->setRawPath($route->getRawPath());
        $this->addPreFilters($route->getPreFilters());
        $this->addPostFilters($route->getPostFilters());
        $this->setSecure($route->isSecure());
        $this->setVariableRegexes($route->variableRegexes);
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
     * @return string
     */
    public function getPathRegex()
    {
        return $this->pathRegex;
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
     * @param string $regex
     */
    public function setPathRegex($regex)
    {
        $this->pathRegex = $regex;
    }
}
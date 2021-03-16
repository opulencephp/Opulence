<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Routing\Routes;

/**
 * Defines a parsed route
 * This is different than a compiled route because this does not have the context of a particular request
 */
class ParsedRoute extends Route
{
    /** @var string|null The parsed (regex) host if set, otherwise null */
    protected $hostRegex = null;
    /** @var string The parsed (regex) path */
    protected $pathRegex = '';
    /** @var array The mapping of route-variables to their default values */
    protected $defaultValues = [];

    /**
     * @param Route $route The unparsed route whose properties we are copying
     */
    public function __construct(Route $route)
    {
        parent::__construct($route->getMethods(), $route->getRawPath(), $route->getController());

        $this->setName($route->getName());
        $this->setRawHost($route->getRawHost());
        $this->addMiddleware($route->getMiddleware());
        $this->setSecure($route->isSecure());
        $this->setVarRegexes($route->varRegexes);
    }

    /**
     * Gets the default value for a variable
     *
     * @param string $variableName The name of the variable whose default value we want
     * @return mixed|null The default value for the variable if it exists, otherwise null
     */
    public function getDefaultValue(string $variableName)
    {
        if (isset($this->defaultValues[$variableName])) {
            return $this->defaultValues[$variableName];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getHostRegex() : string
    {
        // Default to matching everything if it isn't set
        return $this->hostRegex === null ? '#^.*$#' : $this->hostRegex;
    }

    /**
     * @return string
     */
    public function getPathRegex() : string
    {
        return $this->pathRegex;
    }

    /**
     * Sets a default value for a variable
     *
     * @param string $variableName The name of the variable whose default value we're setting
     * @param mixed $defaultValue The default value for the variable
     */
    public function setDefaultValue(string $variableName, $defaultValue)
    {
        $this->defaultValues[$variableName] = $defaultValue;
    }

    /**
     * @param string $hostRegex
     */
    public function setHostRegex(string $hostRegex)
    {
        $this->hostRegex = $hostRegex;
    }

    /**
     * @param string $regex
     */
    public function setPathRegex(string $regex)
    {
        $this->pathRegex = $regex;
    }
}

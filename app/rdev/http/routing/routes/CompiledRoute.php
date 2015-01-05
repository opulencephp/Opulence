<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a compiled route
 */
namespace RDev\Routing\Routes;

class CompiledRoute extends ParsedRoute
{
    /** @var bool Whether or not this route is a match */
    private $isMatch = false;
    /** @var array The mapping of path variable names to values */
    private $pathVariables = [];

    /**
     * @param ParsedRoute $parsedRoute The parsed route that was compiled
     * @param bool $isMatch Whether or not this route is a match for the request
     * @param array $pathVariables The mapping of path variable names to values
     */
    public function __construct(ParsedRoute $parsedRoute, $isMatch, array $pathVariables = [])
    {
        parent::__construct($parsedRoute);

        $this->isMatch = $isMatch;
        $this->pathVariables = $pathVariables;
        $this->hostRegex = $parsedRoute->hostRegex;
        $this->pathRegex = $parsedRoute->pathRegex;
        $this->defaultValues = $parsedRoute->defaultValues;
    }

    /**
     * @return array
     */
    public function getPathVariables()
    {
        return $this->pathVariables;
    }

    /**
     * @return bool
     */
    public function isMatch()
    {
        return $this->isMatch;
    }

    /**
     * @param bool $isMatch
     */
    public function setMatch($isMatch)
    {
        $this->isMatch = $isMatch;
    }

    /**
     * @param array $pathVariables
     */
    public function setPathVariables($pathVariables)
    {
        $this->pathVariables = $pathVariables;
    }
}
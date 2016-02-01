<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Routing\Routes;

/**
 * Defines a compiled route
 */
class CompiledRoute extends ParsedRoute
{
    /** @var bool Whether or not this route is a match */
    private $isMatch = false;
    /** @var array The mapping of path variable names to values */
    private $pathVars = [];

    /**
     * @param ParsedRoute $parsedRoute The parsed route that was compiled
     * @param bool $isMatch Whether or not this route is a match for the request
     * @param array $pathVars The mapping of path variable names to values
     */
    public function __construct(ParsedRoute $parsedRoute, bool $isMatch, array $pathVars = [])
    {
        parent::__construct($parsedRoute);

        $this->isMatch = $isMatch;
        $this->pathVars = $pathVars;
        $this->hostRegex = $parsedRoute->hostRegex;
        $this->pathRegex = $parsedRoute->pathRegex;
        $this->defaultValues = $parsedRoute->defaultValues;
    }

    /**
     * Gets the value of a path variable
     *
     * @param string $name The name of the variable to get
     * @return mixed|null The value of the variable if it exists, otherwise null
     */
    public function getPathVar(string $name)
    {
        if (isset($this->pathVars[$name])) {
            return $this->pathVars[$name];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getPathVars() : array
    {
        return $this->pathVars;
    }

    /**
     * @return bool
     */
    public function isMatch() : bool
    {
        return $this->isMatch;
    }

    /**
     * @param bool $isMatch
     */
    public function setMatch(bool $isMatch)
    {
        $this->isMatch = $isMatch;
    }

    /**
     * @param array $pathVars
     */
    public function setPathVars($pathVars)
    {
        $this->pathVars = $pathVars;
    }
}
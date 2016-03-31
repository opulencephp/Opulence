<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Http\Middleware;

/**
 * Defines a parameterized middleware
 */
abstract class ParameterizedMiddleware implements IMiddleware
{
    /** @var array The middleware parameters */
    private $parameters = [];

    /**
     * Creates middleware parameters to be used by this middleware
     *
     * @param array $parameters The parameters to include in this middleware
     * @return MiddlewareParameters The middleware parameters
     */
    public static function withParameters(array $parameters) : MiddlewareParameters
    {
        return new MiddlewareParameters(static::class, $parameters);
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Gets the value of a parameter
     *
     * @param string $name The name of the parameter to get
     * @param mixed $default The default value
     * @return mixed|null The parameter's value if it is set, otherwise null
     */
    protected function getParameter(string $name, $default = null)
    {
        if (!array_key_exists($name, $this->parameters)) {
            return $default;
        }

        return $this->parameters[$name];
    }
}
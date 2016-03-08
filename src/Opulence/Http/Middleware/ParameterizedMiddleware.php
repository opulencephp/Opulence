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
    protected $parameters = [];

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
}
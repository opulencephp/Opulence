<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Routing\Dispatchers;

/**
 * Defines the interface for dependency resolvers to implement
 */
interface IDependencyResolver
{
    /**
     * Resolves the interface and returns an instance
     *
     * @param string $interface The interface to resolve
     * @return mixed The resolved object
     * @throws DependencyResolutionException Thrown if the interface could not be resolved
     */
    public function resolve(string $interface);
}

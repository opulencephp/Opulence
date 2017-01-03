<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Ioc\Bootstrappers;

use InvalidArgumentException;

/**
 * Defines the interface for bootstrapper resolvers to implement
 */
interface IBootstrapperResolver
{
    /**
     * Resolves a bootstrapper
     *
     * @param string $bootstrapperClass The class to resolve
     * @return Bootstrapper The resolved bootstrapper
     * @throws InvalidArgumentException Thrown if the class is not a bootstrapper
     */
    public function resolve(string $bootstrapperClass) : Bootstrapper;

    /**
     * Resolves a list of bootstrappers
     *
     * @param array $bootstrapperClasses The list of bootstrapper classes
     * @return Bootstrapper[] The list of bootstrapper objects
     * @throws InvalidArgumentException Thrown if any of the classes are not a resolver
     */
    public function resolveMany(array $bootstrapperClasses) : array;
}
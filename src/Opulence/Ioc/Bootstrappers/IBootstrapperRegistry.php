<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Bootstrappers;

use InvalidArgumentException;

/**
 * Defines the interface for bootstrapper registries to implement
 */
interface IBootstrapperRegistry
{
    /**
     * Gets the list of eager bootstrapper classes
     *
     * @return array The list of eager bootstrapper classes
     */
    public function getEagerBootstrappers() : array;

    /**
     * Gets the mapping of bound classes to their bootstrapper classes
     *
     * @return array The mapping of bound classes to ["bootstrapper" => BootstrapperClass, "target" => TargetClass]
     */
    public function getLazyBootstrapperBindings() : array;

    /**
     * Registers a bootstrapper
     *
     * @param Bootstrapper $bootstrapper The bootstrapper to register
     */
    public function registerBootstrapper(Bootstrapper $bootstrapper);

    /**
     * Registers eager bootstrappers
     *
     * @param string|array $eagerBootstrapperClasses The eager bootstrapper classes
     * @deprecated 1.1.7 Will be removed in next major version
     */
    public function registerEagerBootstrapper($eagerBootstrapperClasses);

    /**
     * Registers bound classes and their bootstrappers
     *
     * @param array $bindings The bindings registered by the bootstrapper
     * @param string $lazyBootstrapperClass The bootstrapper class
     * @throws InvalidArgumentException Thrown if the bindings are not of the correct format
     * @deprecated 1.1.7 Will be removed in next major version
     */
    public function registerLazyBootstrapper(array $bindings, string $lazyBootstrapperClass);

    /**
     * Registers many bootstrappers
     *
     * @param Bootstrapper[] $boostrappers The bootstrappers to register
     */
    public function registerManyBootstrappers(array $boostrappers);
}

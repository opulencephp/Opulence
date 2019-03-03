<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Bootstrappers;

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
    public function registerBootstrapper(Bootstrapper $bootstrapper): void;

    /**
     * Registers many bootstrappers
     *
     * @param Bootstrapper[] $bootstrappers The bootstrappers to register
     */
    public function registerManyBootstrappers(array $bootstrappers): void;
}

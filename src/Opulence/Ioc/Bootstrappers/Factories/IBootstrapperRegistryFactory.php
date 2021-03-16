<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Ioc\Bootstrappers\Factories;

use Opulence\Ioc\Bootstrappers\IBootstrapperRegistry;

/**
 * Defines the interface for bootstrapper registry factories to implement
 */
interface IBootstrapperRegistryFactory
{
    /**
     * Creates a bootstrapper registry
     *
     * @param array $bootstrapperClasses The list of bootstrapper classes
     * @return IBootstrapperRegistry The bootstrapper registry
     */
    public function createBootstrapperRegistry(array $bootstrapperClasses) : IBootstrapperRegistry;
}

<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Bootstrappers\Factories;

use Opulence\Ioc\Bootstrappers\BootstrapperRegistry;
use Opulence\Ioc\Bootstrappers\IBootstrapperRegistry;

/**
 * Defines the bootstrapper registry factory
 */
class BootstrapperRegistryFactory implements IBootstrapperRegistryFactory
{
    /**
     * @inheritdoc
     */
    public function createBootstrapperRegistry(array $bootstrapperClasses) : IBootstrapperRegistry
    {
        $bootstrapperRegistry = new BootstrapperRegistry();

        foreach ($bootstrapperClasses as $bootstrapperClass) {
            $bootstrapperRegistry->registerBootstrapper(new $bootstrapperClass);
        }

        return $bootstrapperRegistry;
    }
}

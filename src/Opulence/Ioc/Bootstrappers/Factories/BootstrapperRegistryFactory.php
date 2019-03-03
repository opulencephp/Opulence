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
use Opulence\Ioc\Bootstrappers\IBootstrapperResolver;

/**
 * Defines the bootstrapper registry factory
 */
class BootstrapperRegistryFactory implements IBootstrapperRegistryFactory
{
    /** @var IBootstrapperResolver The bootstrapper resolver */
    protected $bootstrapperResolver = null;

    /**
     * @param IBootstrapperResolver $bootstrapperResolver The bootstrapper resolver
     */
    public function __construct(IBootstrapperResolver $bootstrapperResolver)
    {
        $this->bootstrapperResolver = $bootstrapperResolver;
    }

    /**
     * @inheritdoc
     */
    public function createBootstrapperRegistry(array $bootstrapperClasses) : IBootstrapperRegistry
    {
        $bootstrapperObjects = $this->bootstrapperResolver->resolveMany($bootstrapperClasses);
        $bootstrapperRegistry = new BootstrapperRegistry();

        foreach ($bootstrapperObjects as $bootstrapperObject) {
            $bootstrapperRegistry->registerBootstrapper($bootstrapperObject);
        }

        return $bootstrapperRegistry;
    }
}

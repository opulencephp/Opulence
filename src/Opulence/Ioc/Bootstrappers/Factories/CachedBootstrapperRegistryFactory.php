<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Ioc\Bootstrappers\Factories;

use Opulence\Ioc\Bootstrappers\Caching\ICache;
use Opulence\Ioc\Bootstrappers\IBootstrapperRegistry;
use Opulence\Ioc\Bootstrappers\IBootstrapperResolver;

/**
 * Defines the cached bootstrapper registry factory
 */
class CachedBootstrapperRegistryFactory extends BootstrapperRegistryFactory
{
    /** @var ICache The bootstrapper registry cache */
    private $bootstrapperRegistryCache;

    /**
     * @inheritdoc
     * @param ICache $bootstrapperRegistryCache The bootstrapper registry cache
     */
    public function __construct(IBootstrapperResolver $bootstrapperResolver, ICache $bootstrapperRegistryCache)
    {
        parent::__construct($bootstrapperResolver);

        $this->bootstrapperRegistryCache = $bootstrapperRegistryCache;
    }

    /**
     * @inheritdoc
     */
    public function createBootstrapperRegistry(array $bootstrapperClasses) : IBootstrapperRegistry
    {
        if (($bootstrapperRegistry = $this->bootstrapperRegistryCache->get()) === null) {
            $bootstrapperRegistry = parent::createBootstrapperRegistry($bootstrapperClasses);
            $this->bootstrapperRegistryCache->set($bootstrapperRegistry);
        }

        return $bootstrapperRegistry;
    }
}
<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Bootstrappers\Caching;

use Opulence\Ioc\Bootstrappers\IBootstrapperRegistry;

/**
 * Defines the interface for bootstrapper caches to implement
 */
interface ICache
{
    /**
     * Flushes the cache
     */
    public function flush();

    /**
     * Gets the bootstrapper registry from cache if it exists
     *
     * @return IBootstrapperRegistry|null The bootstrapper registry if one was found, otherwise null
     */
    public function get();

    /**
     * Writes the bootstrapper registry
     *
     * @param IBootstrapperRegistry $registry The config to write
     */
    public function set(IBootstrapperRegistry $registry);
}

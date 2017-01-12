<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Bootstrappers\Dispatchers;

/**
 * Defines the bootstrapper dispatcher
 */
interface IBootstrapperDispatcher
{
    /**
     * Shuts down the bootstrappers
     */
    public function shutDownBootstrappers();

    /**
     * Starts the bootstrappers
     *
     * @param bool $forceEagerLoading Whether or not to force eager loading
     */
    public function startBootstrappers(bool $forceEagerLoading);
}

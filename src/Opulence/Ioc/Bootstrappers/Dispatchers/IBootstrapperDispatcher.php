<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Bootstrappers\Dispatchers;

use Opulence\Ioc\Bootstrappers\Bootstrapper;

/**
 * Defines the bootstrapper dispatcher
 */
interface IBootstrapperDispatcher
{
    /**
     * Dispatchers a list of bootstrappers
     *
     * @param Bootstrapper[] $bootstrappers The bootstrappers to dispatch
     */
    public function dispatch(array $bootstrappers): void;
}

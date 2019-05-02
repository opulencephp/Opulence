<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Ioc\Bootstrappers;

/**
 * Defines the interface for bootstrapper dispatchers to implement
 */
interface IBootstrapperDispatcher
{
    /**
     * Dispatches bootstrappers
     *
     * @param Bootstrapper[] $bootstrappers The bootstrappers to dispatch
     */
    public function dispatch(array $bootstrappers): void;
}

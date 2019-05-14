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

use Opulence\Ioc\IContainer;
use Opulence\Ioc\ResolutionException;
use RuntimeException;

/**
 * Defines the base class for bootstrappers
 */
abstract class Bootstrapper
{
    final public function __construct()
    {
        // Don't do anything
    }

    /**
     * Registers any bindings to the IoC container
     *
     * @param IContainer $container The IoC container to bind to
     * @throws ResolutionException Thrown if there was any error resolving a dependency
     * @throws RuntimeException Thrown if there was an error registering the bindings
     */
    abstract public function registerBindings(IContainer $container): void;
}

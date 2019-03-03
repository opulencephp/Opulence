<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Bootstrappers;

use Opulence\Ioc\IContainer;
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
     * @throws RuntimeException Thrown if there was an error registering the bindings
     */
    abstract public function registerBindings(IContainer $container) : void;
}

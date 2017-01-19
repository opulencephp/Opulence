<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Bootstrappers;

use BadMethodCallException;
use Opulence\Ioc\IContainer;

/**
 * Defines the base bootstrapper
 */
abstract class Bootstrapper
{
    final public function __construct()
    {
        // Don't do anything
    }

    /**
     * Handles the case that the bootstrapper did not implement the initialize(), run(), or shutdown() methods
     *
     * @param string $name The name of the method to call
     * @param array $arguments The list of arguments to pass in
     * @throws BadMethodCallException Thrown if a method other than "run" is called
     * @deprecated 1.1.0 run() and shutdown() will soon not be supported
     */
    public function __call(string $name, array $arguments)
    {
        if ($name !== 'run' && $name !== 'shutdown') {
            throw new BadMethodCallException(
                sprintf(
                    'Only %s, and %s are supported',
                    'Bootstrapper::run()',
                    'Bootstrapper::shutdown()'
                )
            );
        }

        // The user must have not specified a "run" or "shutdown" function, so just return
        return;
    }

    /**
     * Registers any bindings to the IoC container
     *
     * @param IContainer $container The IoC container to bind to
     */
    public function registerBindings(IContainer $container)
    {
        // Let extending classes define this
    }
}

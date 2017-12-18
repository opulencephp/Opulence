<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Bootstrappers;

/**
 * Defines the lazy bootstrapper
 */
abstract class LazyBootstrapper extends Bootstrapper
{
    /**
     * Gets the list of classes and interfaces bound by this bootstrapper to the IoC container
     *
     * @return array The list of bound classes
     */
    abstract public function getBindings() : array;
}

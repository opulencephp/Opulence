<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Ioc\Bootstrappers;

/**
 * Defines the interface for lazy bootstrappers to implement
 */
interface ILazyBootstrapper
{
    /**
     * Gets the list of classes and interfaces bound by this bootstrapper to the IoC container
     *
     * @return array The list of bound classes
     */
    public function getBindings() : array;
}
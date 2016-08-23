<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Ioc\Bootstrappers;

use InvalidArgumentException;
use RuntimeException;

/**
 * Defines the interface for bootstrapper registries to implement
 */
interface IBootstrapperRegistry
{
    /**
     * Gets the list of eager bootstrapper classes
     *
     * @return array The list of eager bootstrapper classes
     */
    public function getEagerBootstrappers() : array;

    /**
     * Gets the mapping of bound classes to their bootstrapper classes
     *
     * @return array The mapping of bound classes to ["bootstrapper" => BootstrapperClass, "target" => TargetClass]
     */
    public function getLazyBootstrapperBindings() : array;

    /**
     * Registers bootstrapper classes in the case that no cached registry was found
     * In this case, all the bootstrappers in this list are instantiated and later written to a cached registry
     *
     * @param array $bootstrapperClasses The list of bootstrapper classes
     */
    public function registerBootstrappers(array $bootstrapperClasses);

    /**
     * Registers eager bootstrappers
     *
     * @param string|array $eagerBootstrapperClasses The eager bootstrapper classes
     */
    public function registerEagerBootstrapper($eagerBootstrapperClasses);

    /**
     * Registers bound classes and their bootstrappers
     *
     * @param array $bindings The bindings registered by the bootstrapper
     * @param string $lazyBootstrapperClass The bootstrapper class
     * @throws InvalidArgumentException Thrown if the bindings are not of the correct format
     */
    public function registerLazyBootstrapper(array $bindings, string $lazyBootstrapperClass);

    /**
     * Resolves an instance of the bootstrapper class
     *
     * @param string $bootstrapperClass The name of the class whose instance we want
     * @return Bootstrapper The instance of the bootstrapper
     * @throws RuntimeException Thrown if the bootstrapper is not an instance of Bootstrapper
     */
    public function resolve(string $bootstrapperClass) : Bootstrapper;

    /**
     * Sets the eager and lazy bootstrappers
     */
    public function setBootstrapperDetails();
}
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
use Opulence\Ioc\Bootstrappers\IBootstrapperRegistry;
use Opulence\Ioc\Bootstrappers\IBootstrapperResolver;
use Opulence\Ioc\IContainer;
use RuntimeException;

/**
 * Defines the bootstrapper dispatcher
 */
class BootstrapperDispatcher implements IBootstrapperDispatcher
{
    /** @var IContainer The IoC container */
    private $container = null;
    /** @var IBootstrapperRegistry The bootstrapper registry */
    private $bootstrapperRegistry = null;
    /** @var IBootstrapperResolver The bootstrapper resolver */
    private $bootstrapperResolver = null;
    /** @var array The list of bootstrapper classes that have been run */
    private $dispatchedBootstrappers = [];
    /** @var Bootstrapper[] The list of instantiated bootstrappers */
    private $bootstrapperObjects = [];

    /**
     * @param IContainer $container The IoC container
     * @param IBootstrapperRegistry $bootstrapperRegistry The bootstrapper registry
     * @param IBootstrapperResolver $bootstrapperResolver The bootstrapper resolver
     */
    public function __construct(
        IContainer $container,
        IBootstrapperRegistry $bootstrapperRegistry,
        IBootstrapperResolver $bootstrapperResolver
    ) {
        $this->container = $container;
        $this->bootstrapperRegistry = $bootstrapperRegistry;
        $this->bootstrapperResolver = $bootstrapperResolver;
    }

    /**
     * @inheritdoc
     */
    public function dispatch(bool $forceEagerLoading) : void
    {
        if ($forceEagerLoading) {
            $eagerBootstrapperClasses = $this->bootstrapperRegistry->getEagerBootstrappers();
            $lazyBootstrapperClasses = [];

            foreach (array_values($this->bootstrapperRegistry->getLazyBootstrapperBindings()) as $bindingData) {
                $lazyBootstrapperClasses[] = $bindingData['bootstrapper'];
            }

            $lazyBootstrapperClasses = array_unique($lazyBootstrapperClasses);
            $bootstrapperClasses = array_merge($eagerBootstrapperClasses, $lazyBootstrapperClasses);
            $this->dispatchEagerly($bootstrapperClasses, false);
        } else {
            // We must dispatch lazy bootstrappers first in case their bindings are used by eager bootstrappers
            $this->dispatchLazily($this->bootstrapperRegistry->getLazyBootstrapperBindings(), false);
            $this->dispatchEagerly($this->bootstrapperRegistry->getEagerBootstrappers(), false);
        }
    }

    /**
     * @inheritdoc
     */
    public function shutDownBootstrappers()
    {
        foreach ($this->bootstrapperObjects as $bootstrapper) {
            $this->container->callMethod($bootstrapper, 'shutdown', [], true);
        }
    }

    /**
     * @inheritdoc
     */
    public function startBootstrappers(bool $forceEagerLoading)
    {
        if ($forceEagerLoading) {
            $eagerBootstrapperClasses = $this->bootstrapperRegistry->getEagerBootstrappers();
            $lazyBootstrapperClasses = [];

            foreach (array_values($this->bootstrapperRegistry->getLazyBootstrapperBindings()) as $bindingData) {
                $lazyBootstrapperClasses[] = $bindingData['bootstrapper'];
            }

            $lazyBootstrapperClasses = array_unique($lazyBootstrapperClasses);
            $bootstrapperClasses = array_merge($eagerBootstrapperClasses, $lazyBootstrapperClasses);
            $this->dispatchEagerly($bootstrapperClasses, true);
        } else {
            // We must dispatch lazy bootstrappers first in case their bindings are used by eager bootstrappers
            $this->dispatchLazily($this->bootstrapperRegistry->getLazyBootstrapperBindings(), true);
            $this->dispatchEagerly($this->bootstrapperRegistry->getEagerBootstrappers(), true);
        }
    }

    /**
     * Dispatches the registry eagerly
     *
     * @param array $bootstrapperClasses The list of bootstrapper classes to dispatch
     * @param bool $run Whether or not to run the bootstrapper (deprecated)
     * @throws RuntimeException Thrown if there was a problem dispatching the bootstrappers
     */
    private function dispatchEagerly(array $bootstrapperClasses, bool $run)
    {
        foreach ($bootstrapperClasses as $bootstrapperClass) {
            /** @var Bootstrapper $bootstrapper */
            $bootstrapper = $this->bootstrapperResolver->resolve($bootstrapperClass);
            $this->bootstrapperObjects[] = $bootstrapper;
            $bootstrapper->registerBindings($this->container);
        }

        if ($run) {
            foreach ($this->bootstrapperObjects as $bootstrapper) {
                $this->container->callMethod($bootstrapper, 'run', [], true);
            }
        }
    }

    /**
     * Dispatches the registry lazily
     *
     * @param array $boundClassesToBindingData The mapping of bound classes to their targets and bootstrappers
     * @param bool $run Whether or not to run the bootstrapper (deprecated)
     * @throws RuntimeException Thrown if there was a problem dispatching the bootstrappers
     */
    private function dispatchLazily(array $boundClassesToBindingData, bool $run)
    {
        foreach ($boundClassesToBindingData as $boundClass => $bindingData) {
            $bootstrapperClass = $bindingData['bootstrapper'];
            $target = $bindingData['target'];

            $factory = function () use ($boundClass, $bootstrapperClass, $target, $run) {
                // To make sure this factory isn't used anymore to resolve the bound class, unbind it
                // Otherwise, we'd get into an infinite loop every time we tried to resolve it
                if ($target === null) {
                    $this->container->unbind($boundClass);
                } else {
                    $this->container->for($target, function (IContainer $container) use ($boundClass) {
                        $container->unbind($boundClass);
                    });
                }

                $bootstrapper = $this->bootstrapperResolver->resolve($bootstrapperClass);

                if (!in_array($bootstrapper, $this->bootstrapperObjects)) {
                    $this->bootstrapperObjects[] = $bootstrapper;
                }

                if (!isset($this->dispatchedBootstrappers[$bootstrapperClass])) {
                    $bootstrapper->registerBindings($this->container);

                    if ($run) {
                        $this->container->callMethod($bootstrapper, 'run', [], true);
                    }

                    $this->dispatchedBootstrappers[$bootstrapperClass] = true;
                }

                if ($target === null) {
                    return $this->container->resolve($boundClass);
                } else {
                    return $this->container->for($target, function (IContainer $container) use ($boundClass) {
                        return $container->resolve($boundClass);
                    });
                }
            };

            if ($target === null) {
                $this->container->bindFactory($boundClass, $factory);
            } else {
                $this->container->for($target, function (IContainer $container) use ($boundClass, $factory) {
                    $container->bindFactory($boundClass, $factory);
                });
            }
        }
    }
}

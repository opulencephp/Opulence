<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Bootstrappers\Dispatchers;

use InvalidArgumentException;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\LazyBootstrapper;
use Opulence\Ioc\IContainer;
use RuntimeException;

/**
 * Defines the bootstrapper dispatcher
 */
class BootstrapperDispatcher implements IBootstrapperDispatcher
{
    /** @var IContainer The IoC container */
    private $container;
    /** @var array The list of bootstrapper classes that have been run */
    private $dispatchedBootstrappers = [];
    /** @var Bootstrapper[] The list of instantiated bootstrappers */
    private $bootstrapperObjects = [];

    /**
     * @param IContainer $container The IoC container
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function dispatch(array $bootstrappers) : void
    {
        foreach ($bootstrappers as $bootstrapper) {
            if ($bootstrapper instanceof LazyBootstrapper) {
                $this->dispatchLazyBootstrapper($bootstrapper);
            } else {
                $this->dispatchEagerBootstrapper($bootstrapper);
            }
        }
    }

    /**
     * Dispatches an eager bootstrapper
     *
     * @param Bootstrapper $bootstrapper The bootstrapper to dispatch
     * @throws RuntimeException Thrown if there was a problem dispatching the bootstrappers
     */
    private function dispatchEagerBootstrapper(Bootstrapper $bootstrapper) : void
    {
        $this->bootstrapperObjects[] = $bootstrapper;
        $bootstrapper->registerBindings($this->container);
    }

    /**
     * Dispatches a bootstrapper lazily
     *
     * @param LazyBootstrapper $bootstrapper The bootstrapper to dispatch
     * @throws RuntimeException Thrown if there was a problem dispatching the bootstrappers
     */
    private function dispatchLazyBootstrapper(LazyBootstrapper $bootstrapper) : void
    {
        foreach ($bootstrapper->getBindings() as $binding) {
            // If it's a targeted binding
            if (is_array($binding)) {
                if (count($binding) !== 1) {
                    throw new InvalidArgumentException(
                        'Targeted bindings must be in format "BoundClass => TargetClass"'
                    );
                }

                $targetClass = array_values($binding)[0];
                $boundClass = array_keys($binding)[0];
            } else {
                $boundClass = $binding;
                $targetClass = null;
            }

            $factory = function () use ($boundClass, $bootstrapper, $targetClass) {
                $bootstrapperClass = \get_class($bootstrapper);
                // To make sure this factory isn't used anymore to resolve the bound class, unbind it
                // Otherwise, we'd get into an infinite loop every time we tried to resolve it
                if ($targetClass === null) {
                    $this->container->unbind($boundClass);
                } else {
                    $this->container->for($targetClass, function (IContainer $container) use ($boundClass) {
                        $container->unbind($boundClass);
                    });
                }

                if (!in_array($bootstrapper, $this->bootstrapperObjects, true)) {
                    $this->bootstrapperObjects[] = $bootstrapper;
                }

                if (!isset($this->dispatchedBootstrappers[$bootstrapperClass])) {
                    $bootstrapper->registerBindings($this->container);
                    $this->dispatchedBootstrappers[$bootstrapperClass] = true;
                }

                if ($targetClass === null) {
                    return $this->container->resolve($boundClass);
                }

                return $this->container->for($targetClass, function (IContainer $container) use ($boundClass) {
                    return $container->resolve($boundClass);
                });
            };

            if ($targetClass === null) {
                $this->container->bindFactory($boundClass, $factory);
            } else {
                $this->container->for($targetClass, function (IContainer $container) use ($boundClass, $factory) {
                    $container->bindFactory($boundClass, $factory);
                });
            }
        }
    }
}

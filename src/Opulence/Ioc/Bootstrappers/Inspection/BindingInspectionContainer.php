<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Ioc\Bootstrappers\Inspection;

use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\IContainer;

/**
 * Defines a container that can be used to inspect the bindings set in a bootstrapper
 * @internal
 */
final class BindingInspectionContainer implements IContainer
{
    /** @var InspectionBinding[] The inspection bindings that were found */
    private $bindings = [];
    /** @var string|null The current target class */
    private $currTargetClass;
    /** @var Bootstrapper The current bootstrapper class */
    private $currBootstrapper;

    /**
     * @inheritdoc
     */
    public function bindFactory($interfaces, callable $factory, bool $resolveAsSingleton = false): void
    {
        foreach ((array)$interfaces as $interface) {
            $this->bindings[] = $this->createInspectionBinding($interface);
        }
    }

    /**
     * @inheritdoc
     */
    public function bindInstance($interfaces, $instance): void
    {
        foreach ((array)$interfaces as $interface) {
            $this->bindings[] = $this->createInspectionBinding($interface);
        }
    }

    /**
     * @inheritdoc
     */
    public function bindPrototype($interfaces, string $concreteClass = null, array $primitives = []): void
    {
        foreach ((array)$interfaces as $interface) {
            $this->bindings[] = $this->createInspectionBinding($interface);
        }
    }

    /**
     * @inheritdoc
     */
    public function bindSingleton($interfaces, string $concreteClass = null, array $primitives = []): void
    {
        foreach ((array)$interfaces as $interface) {
            $this->bindings[] = $this->createInspectionBinding($interface);
        }
    }

    public function callClosure(callable $closure, array $primitives = [])
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function callMethod($instance, string $methodName, array $primitives = [], bool $ignoreMissingMethod = false)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function for(string $targetClass, callable $callback)
    {
        $this->currTargetClass = $targetClass;
        $callback($this);
        $this->currTargetClass = null;
    }

    /**
     * Gets all the bindings that were found
     *
     * @return InspectionBinding[] The bindings that were found
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }

    /**
     * @inheritdoc
     */
    public function hasBinding(string $interface): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function resolve(string $interface)
    {
        return null;
    }

    public function setBootstrapper(Bootstrapper $bootstrapper): void
    {
        $this->currBootstrapper = $bootstrapper;
    }

    /**
     * @inheritdoc
     */
    public function tryResolve(string $interface, &$instance): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function unbind($interfaces): void
    {
        // Don't do anything
    }

    /**
     * Creates an inspection binding
     *
     * @param string $interface The interface that was bound
     * @return InspectionBinding The binding for the interface
     */
    private function createInspectionBinding(string $interface): InspectionBinding
    {
        return $this->currTargetClass === null
            ? new UniversalInspectionBinding($interface, $this->currBootstrapper)
            : new TargetedInspectionBinding($this->currTargetClass, $interface, $this->currBootstrapper);
    }
}

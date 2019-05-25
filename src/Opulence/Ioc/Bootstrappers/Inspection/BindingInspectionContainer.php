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
use Opulence\Ioc\Container;

/**
 * Defines a container that can be used to inspect the bindings set in a bootstrapper
 * @internal
 */
final class BindingInspectionContainer extends Container
{
    /** @var BootstrapperBinding[] The bootstrapper bindings that were found */
    private $bootstrapperBindings = [];
    /** @var Bootstrapper The current bootstrapper class */
    private $currBootstrapper;

    /**
     * @inheritdoc
     */
    public function bindFactory($interfaces, callable $factory, bool $resolveAsSingleton = false): void
    {
        $this->addBootstrapperBinding($interfaces);
        parent::bindFactory($interfaces, $factory, $resolveAsSingleton);
    }

    /**
     * @inheritdoc
     */
    public function bindInstance($interfaces, object $instance): void
    {
        $this->addBootstrapperBinding($interfaces);
        parent::bindInstance($interfaces, $instance);
    }

    /**
     * @inheritdoc
     */
    public function bindPrototype($interfaces, string $concreteClass = null, array $primitives = []): void
    {
        $this->addBootstrapperBinding($interfaces);
        parent::bindPrototype($interfaces, $concreteClass, $primitives);
    }

    /**
     * @inheritdoc
     */
    public function bindSingleton($interfaces, string $concreteClass = null, array $primitives = []): void
    {
        $this->addBootstrapperBinding($interfaces);
        parent::bindSingleton($interfaces, $concreteClass, $primitives);
    }

    /**
     * Gets all the bindings that were found
     *
     * @return BootstrapperBinding[] The bindings that were found
     */
    public function getBindings(): array
    {
        // We don't want the keys returned
        $bootstrapperBindings = [];

        foreach ($this->bootstrapperBindings as $interface => $bindings) {
            $bootstrapperBindings = \array_merge($bootstrapperBindings, $bindings);
        }

        return $bootstrapperBindings;
    }

    public function setBootstrapper(Bootstrapper $bootstrapper): void
    {
        $this->currBootstrapper = $bootstrapper;
    }

    /**
     * Adds a binding to the container if it does not already exist
     *
     * @param array|string $interfaces The interface or interfaces we're registering a binding for
     */
    private function addBootstrapperBinding($interfaces): void
    {
        foreach ((array)$interfaces as $interface) {
            $bootstrapperBinding = $this->createBootstrapperBinding($interface);
            $isTargetedBinding = $bootstrapperBinding instanceof TargetedBootstrapperBinding;

            if (!isset($this->bootstrapperBindings[$interface])) {
                $this->bootstrapperBindings[$interface] = [$bootstrapperBinding];
                continue;
            }

            // Check if this exact binding has already been registered
            $bindingAlreadyExists = false;

            /** @var BootstrapperBinding $existingBootstrapperBinding */
            foreach ($this->bootstrapperBindings[$interface] as $existingBootstrapperBinding) {
                if (
                    $bootstrapperBinding->getInterface() !== $existingBootstrapperBinding->getInterface()
                    || $bootstrapperBinding->getBootstrapper() !== $existingBootstrapperBinding->getBootstrapper()
                ) {
                    continue;
                }

                if ($isTargetedBinding) {
                    if ($existingBootstrapperBinding instanceof TargetedBootstrapperBinding) {
                        $bindingAlreadyExists = true;
                        break;
                    }
                } elseif ($existingBootstrapperBinding instanceof UniversalBootstrapperBinding) {
                    $bindingAlreadyExists = true;
                    break;
                }
            }

            if (!$bindingAlreadyExists) {
                $this->bootstrapperBindings[$interface][] = $bootstrapperBinding;
            }
        }
    }

    /**
     * Creates an inspection binding
     *
     * @param string $interface The interface that was bound
     * @return BootstrapperBinding The binding for the interface
     */
    private function createBootstrapperBinding(string $interface): BootstrapperBinding
    {
        return $this->currentTarget === null
            ? new UniversalBootstrapperBinding($interface, $this->currBootstrapper)
            : new TargetedBootstrapperBinding($this->currentTarget, $interface, $this->currBootstrapper);
    }
}

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
    /** @var InspectionBinding[] The inspection bindings that were found */
    private $inspectionBindings = [];
    /** @var Bootstrapper The current bootstrapper class */
    private $currBootstrapper;

    /**
     * @inheritdoc
     */
    public function bindFactory($interfaces, callable $factory, bool $resolveAsSingleton = false): void
    {
        $this->addInspectionBinding($interfaces);
        parent::bindFactory($interfaces, $factory, $resolveAsSingleton);
    }

    /**
     * @inheritdoc
     */
    public function bindInstance($interfaces, $instance): void
    {
        $this->addInspectionBinding($interfaces);
        parent::bindInstance($interfaces, $instance);
    }

    /**
     * @inheritdoc
     */
    public function bindPrototype($interfaces, string $concreteClass = null, array $primitives = []): void
    {
        $this->addInspectionBinding($interfaces);
        parent::bindPrototype($interfaces, $concreteClass, $primitives);
    }

    /**
     * @inheritdoc
     */
    public function bindSingleton($interfaces, string $concreteClass = null, array $primitives = []): void
    {
        $this->addInspectionBinding($interfaces);
        parent::bindSingleton($interfaces, $concreteClass, $primitives);
    }

    /**
     * Gets all the bindings that were found
     *
     * @return InspectionBinding[] The bindings that were found
     */
    public function getBindings(): array
    {
        // We don't want the keys returned
        $inspectionBindings = [];

        foreach ($this->inspectionBindings as $interface => $bindings) {
            $inspectionBindings = \array_merge($inspectionBindings, $bindings);
        }

        return $inspectionBindings;
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
    private function addInspectionBinding($interfaces): void
    {
        foreach ((array)$interfaces as $interface) {
            $inspectionBinding = $this->createInspectionBinding($interface);
            $isTargetedBinding = $inspectionBinding instanceof TargetedInspectionBinding;

            if (!isset($this->inspectionBindings[$interface])) {
                $this->inspectionBindings[$interface] = [$inspectionBinding];
                continue;
            }

            // Check if this exact binding has already been registered
            $bindingAlreadyExists = false;

            /** @var InspectionBinding $existingInspectionBinding */
            foreach ($this->inspectionBindings[$interface] as $existingInspectionBinding) {
                if (
                    $inspectionBinding->getInterface() !== $existingInspectionBinding->getInterface()
                    || $inspectionBinding->getBootstrapper() !== $existingInspectionBinding->getBootstrapper()
                ) {
                    continue;
                }

                if ($isTargetedBinding) {
                    if ($existingInspectionBinding instanceof TargetedInspectionBinding) {
                        $bindingAlreadyExists = true;
                        break;
                    }
                } elseif ($existingInspectionBinding instanceof UniversalInspectionBinding) {
                    $bindingAlreadyExists = true;
                    break;
                }
            }

            if (!$bindingAlreadyExists) {
                $this->inspectionBindings[$interface][] = $inspectionBinding;
            }
        }
    }

    /**
     * Creates an inspection binding
     *
     * @param string $interface The interface that was bound
     * @return InspectionBinding The binding for the interface
     */
    private function createInspectionBinding(string $interface): InspectionBinding
    {
        return $this->currentTarget === null
            ? new UniversalInspectionBinding($interface, $this->currBootstrapper)
            : new TargetedInspectionBinding($this->currentTarget, $interface, $this->currBootstrapper);
    }
}

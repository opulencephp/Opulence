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

use Opulence\Ioc\IContainer;

/**
 * Defines what registers our lazy bindings to the container
 */
final class LazyBindingRegistrant
{
    /** @var IContainer The container to bind our resolvers to */
    private $container;
    /** @var array The list of already-dispatched bootstrapper classes */
    private $alreadyDispatchedBootstrapperClasses = [];

    /**
     * @param IContainer $container The container to bind our resolvers to
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * Registers bindings found during inspection
     *
     * @param InspectionBinding[] $inspectionBindings The bindings whose resolvers we're going to register
     */
    public function registerBindings(array $inspectionBindings): void
    {
        foreach ($inspectionBindings as $inspectionBinding) {
            $resolvingFactory = function () use ($inspectionBinding) {
                /**
                 * To make sure this factory isn't used anymore to resolve the bound class, unbind it and rely on the
                 * binding defined in the bootstrapper.  Otherwise, we'd get into an infinite loop every time we tried
                 * to resolve it.
                 */
                if ($inspectionBinding instanceof TargetedInspectionBinding) {
                    $this->container->for($inspectionBinding->getTargetClass(), function (IContainer $container) use ($inspectionBinding) {
                        $container->unbind($inspectionBinding->getInterface());
                    });
                } else {
                    $this->container->unbind($inspectionBinding->getInterface());
                }

                $bootstrapper = $inspectionBinding->getBootstrapper();
                $bootstrapperClass = \get_class($bootstrapper);

                // Make sure we don't double-dispatch this bootstrapper
                if (!isset($this->alreadyDispatchedBootstrapperClasses[$bootstrapperClass])) {
                    $bootstrapper->registerBindings($this->container);
                    $this->alreadyDispatchedBootstrapperClasses[$bootstrapperClass] = true;
                }

                if ($inspectionBinding instanceof TargetedInspectionBinding) {
                    return $this->container->for($inspectionBinding->getTargetClass(), function (IContainer $container) use ($inspectionBinding) {
                        return $container->resolve($inspectionBinding->getInterface());
                    });
                }

                return $this->container->resolve($inspectionBinding->getInterface());
            };

            if ($inspectionBinding instanceof TargetedInspectionBinding) {
                $this->container->for($inspectionBinding->getTargetClass(), function (IContainer $container) use ($inspectionBinding, $resolvingFactory) {
                    $container->bindFactory($inspectionBinding->getInterface(), $resolvingFactory);
                });
            } else {
                $this->container->bindFactory($inspectionBinding->getInterface(), $resolvingFactory);
            }
        }
    }
}

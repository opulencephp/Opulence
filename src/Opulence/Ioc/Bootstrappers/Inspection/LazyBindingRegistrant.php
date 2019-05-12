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
     * @param BootstrapperBinding[] $bootstrapperBindings The bindings whose resolvers we're going to register
     */
    public function registerBindings(array $bootstrapperBindings): void
    {
        foreach ($bootstrapperBindings as $bootstrapperBinding) {
            $resolvingFactory = function () use ($bootstrapperBinding) {
                /**
                 * To make sure this factory isn't used anymore to resolve the bound interface, unbind it and rely on the
                 * binding defined in the bootstrapper.  Otherwise, we'd get into an infinite loop every time we tried
                 * to resolve it.
                 */
                if ($bootstrapperBinding instanceof TargetedBootstrapperBinding) {
                    $this->container->for($bootstrapperBinding->getTargetClass(), function (IContainer $container) use ($bootstrapperBinding) {
                        $container->unbind($bootstrapperBinding->getInterface());
                    });
                } else {
                    $this->container->unbind($bootstrapperBinding->getInterface());
                }

                $bootstrapper = $bootstrapperBinding->getBootstrapper();
                $bootstrapperClass = \get_class($bootstrapper);

                // Make sure we don't double-dispatch this bootstrapper
                if (!isset($this->alreadyDispatchedBootstrapperClasses[$bootstrapperClass])) {
                    $bootstrapper->registerBindings($this->container);
                    $this->alreadyDispatchedBootstrapperClasses[$bootstrapperClass] = true;
                }

                if ($bootstrapperBinding instanceof TargetedBootstrapperBinding) {
                    return $this->container->for($bootstrapperBinding->getTargetClass(), function (IContainer $container) use ($bootstrapperBinding) {
                        return $container->resolve($bootstrapperBinding->getInterface());
                    });
                }

                return $this->container->resolve($bootstrapperBinding->getInterface());
            };

            if ($bootstrapperBinding instanceof TargetedBootstrapperBinding) {
                $this->container->for($bootstrapperBinding->getTargetClass(), function (IContainer $container) use ($bootstrapperBinding, $resolvingFactory) {
                    $container->bindFactory($bootstrapperBinding->getInterface(), $resolvingFactory);
                });
            } else {
                $this->container->bindFactory($bootstrapperBinding->getInterface(), $resolvingFactory);
            }
        }
    }
}

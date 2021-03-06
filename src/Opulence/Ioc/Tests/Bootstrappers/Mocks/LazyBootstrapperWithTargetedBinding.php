<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Ioc\Tests\Bootstrappers\Mocks;

use Opulence\Ioc\Bootstrappers\Bootstrapper as BaseBootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

/**
 * Mocks a lazy bootstrapper with a targeted binding
 */
class LazyBootstrapperWithTargetedBinding extends BaseBootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings() : array
    {
        return [
            [LazyFooInterface::class => EagerBootstrapperThatDependsOnBindingFromLazyBootstrapper::class]
        ];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $container->for(
            EagerBootstrapperThatDependsOnBindingFromLazyBootstrapper::class,
            function (IContainer $container) {
                $container->bindSingleton(LazyFooInterface::class, LazyConcreteFoo::class);
            }
        );
    }
}

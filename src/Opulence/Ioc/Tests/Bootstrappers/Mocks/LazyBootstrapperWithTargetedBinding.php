<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Tests\Bootstrappers\Mocks;

use Opulence\Ioc\Bootstrappers\LazyBootstrapper;
use Opulence\Ioc\IContainer;

/**
 * Mocks a lazy bootstrapper with a targeted binding
 */
class LazyBootstrapperWithTargetedBinding extends LazyBootstrapper
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
    public function registerBindings(IContainer $container) : void
    {
        $container->for(
            EagerBootstrapperThatDependsOnBindingFromLazyBootstrapper::class,
            function (IContainer $container) {
                $container->bindSingleton(LazyFooInterface::class, LazyConcreteFoo::class);
            }
        );
    }
}

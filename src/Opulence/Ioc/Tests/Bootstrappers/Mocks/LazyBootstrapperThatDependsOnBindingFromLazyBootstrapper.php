<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Tests\Bootstrappers\Mocks;

use Opulence\Ioc\Bootstrappers\Bootstrapper as BaseBootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

/**
 * Defines a bootstrapper that depends on a binding from a lazy bootstrapper
 */
class LazyBootstrapperThatDependsOnBindingFromLazyBootstrapper extends BaseBootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings() : array
    {
        return [EagerFooInterface::class];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $container->bindSingleton(EagerFooInterface::class, EagerConcreteFoo::class);
        $foo = $container->resolve(LazyFooInterface::class);
        echo $foo->getClass();
    }
}

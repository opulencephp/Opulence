<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a bootstrapper that depends on a binding from a lazy bootstrapper
 */
namespace Opulence\Tests\Applications\Bootstrappers\Mocks;

use Opulence\Applications\Bootstrappers\Bootstrapper as BaseBootstrapper;
use Opulence\Applications\Bootstrappers\ILazyBootstrapper;
use Opulence\IoC\IContainer;

class LazyBootstrapperThatDependsOnBindingFromLazyBootstrapper extends BaseBootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings()
    {
        return [EagerFooInterface::class];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $container->bind(EagerFooInterface::class, EagerConcreteFoo::class);
    }

    /**
     * Runs this bootstrapper
     *
     * @param LazyFooInterface $foo The dependency set in a lazy bootstrapper
     */
    public function run(LazyFooInterface $foo)
    {
        echo $foo->getClass();
    }
}
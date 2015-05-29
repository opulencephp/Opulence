<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a bootstrapper that depends on a binding from a lazy bootstrapper
 */
namespace RDev\Tests\Applications\Bootstrappers\Mocks;
use RDev\Applications\Bootstrappers\Bootstrapper as BaseBootstrapper;
use RDev\Applications\Bootstrappers\ILazyBootstrapper;
use RDev\IoC\IContainer;

class LazyBootstrapperThatDependsOnBindingFromLazyBootstrapper extends BaseBootstrapper implements ILazyBootstrapper
{
    /**
     * {@inheritdoc}
     */
    public function getBoundClasses()
    {
        return [EagerFooInterface::class];
    }

    /**
     * {@inheritdoc}
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
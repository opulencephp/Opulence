<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a lazy bootstrapper
 */
namespace RDev\Tests\Applications\Bootstrappers\Mocks;
use RDev\Applications\Bootstrappers\Bootstrapper as BaseBootstrapper;
use RDev\Applications\Bootstrappers\ILazyBootstrapper;
use RDev\IoC\IContainer;

class LazyBootstrapper extends BaseBootstrapper implements ILazyBootstrapper
{
    /**
     * {@inheritdoc}
     */
    public function getBoundClasses()
    {
        return [LazyFooInterface::class];
    }

    /**
     * {@inheritdoc}
     */
    public function registerBindings(IContainer $container)
    {
        $container->bind(LazyFooInterface::class, LazyConcreteFoo::class);
    }
}
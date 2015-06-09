<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a bootstrapper that manipulates the environment
 */
namespace RDev\Tests\Applications\Bootstrappers\Mocks;
use RDev\Applications\Bootstrappers\Bootstrapper as BaseBootstrapper;
use RDev\Applications\Bootstrappers\ILazyBootstrapper;
use RDev\IoC\IContainer;

class EnvironmentBootstrapper extends BaseBootstrapper implements ILazyBootstrapper
{
    /**
     * {@inheritdoc}
     */
    public function getBindings()
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

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->environment->setName("running");
    }

    /**
     * {@inheritdoc}
     */
    public function shutdown()
    {
        $this->environment->setName("shutting down");
    }
}
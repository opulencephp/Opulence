<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a bootstrapper that manipulates the environment
 */
namespace Opulence\Tests\Applications\Bootstrappers\Mocks;
use Opulence\Applications\Bootstrappers\Bootstrapper as BaseBootstrapper;
use Opulence\Applications\Bootstrappers\ILazyBootstrapper;
use Opulence\IoC\IContainer;

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
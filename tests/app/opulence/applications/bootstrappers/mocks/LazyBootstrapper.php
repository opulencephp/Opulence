<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a lazy bootstrapper
 */
namespace Opulence\Tests\Applications\Bootstrappers\Mocks;
use Opulence\Applications\Bootstrappers\Bootstrapper as BaseBootstrapper;
use Opulence\Applications\Bootstrappers\ILazyBootstrapper;
use Opulence\IoC\IContainer;

class LazyBootstrapper extends BaseBootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings()
    {
        return [LazyFooInterface::class];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $container->bind(LazyFooInterface::class, LazyConcreteFoo::class);
    }
}
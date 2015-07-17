<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a lazy bootstrapper
 */
namespace Opulence\Tests\Applications\Bootstrappers\Mocks;
use Opulence\Applications\Bootstrappers\Bootstrapper as BaseBootstrapper;
use Opulence\IoC\IContainer;

class EagerBootstrapper extends BaseBootstrapper
{
    /**
     * {@inheritdoc}
     */
    public function registerBindings(IContainer $container)
    {
        $container->bind(EagerFooInterface::class, EagerConcreteFoo::class);
    }
}
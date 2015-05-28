<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a lazy bootstrapper
 */
namespace RDev\Tests\Applications\Bootstrappers\Mocks;
use RDev\Applications\Bootstrappers\Bootstrapper as BaseBootstrapper;
use RDev\IoC\IContainer;

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
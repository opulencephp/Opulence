<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a bootstrapper that depends on a binding from a lazy bootstrapper
 */
namespace RDev\Tests\Applications\Bootstrappers\Mocks;
use RDev\Applications\Bootstrappers\Bootstrapper as BaseBootstrapper;

class EagerBootstrapperThatDependsOnBindingFromLazyBootstrapper extends BaseBootstrapper
{
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
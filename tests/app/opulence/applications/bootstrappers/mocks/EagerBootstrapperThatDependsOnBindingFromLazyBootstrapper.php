<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a bootstrapper that depends on a binding from a lazy bootstrapper
 */
namespace Opulence\Tests\Applications\Bootstrappers\Mocks;

use Opulence\Applications\Bootstrappers\Bootstrapper as BaseBootstrapper;

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
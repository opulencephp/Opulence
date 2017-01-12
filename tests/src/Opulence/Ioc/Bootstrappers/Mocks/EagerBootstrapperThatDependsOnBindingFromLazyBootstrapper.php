<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Tests\Ioc\Bootstrappers\Mocks;

use Opulence\Ioc\Bootstrappers\Bootstrapper as BaseBootstrapper;

/**
 * Defines a bootstrapper that depends on a binding from a lazy bootstrapper
 */
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

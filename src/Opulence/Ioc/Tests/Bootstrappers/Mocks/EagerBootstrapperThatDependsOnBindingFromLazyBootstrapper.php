<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Ioc\Tests\Bootstrappers\Mocks;

use Opulence\Ioc\Bootstrappers\Bootstrapper as BaseBootstrapper;
use Opulence\Ioc\IContainer;

/**
 * Defines a bootstrapper that depends on a binding from a lazy bootstrapper
 */
class EagerBootstrapperThatDependsOnBindingFromLazyBootstrapper extends BaseBootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $foo = $container->resolve(LazyFooInterface::class);
        echo $foo->getClass();
    }
}

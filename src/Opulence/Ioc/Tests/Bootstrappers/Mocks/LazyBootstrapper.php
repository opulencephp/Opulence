<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Tests\Bootstrappers\Mocks;

use Opulence\Ioc\Bootstrappers\Bootstrapper as BaseBootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

/**
 * Mocks a lazy bootstrapper
 */
class LazyBootstrapper extends BaseBootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings() : array
    {
        return [LazyFooInterface::class];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container) : void
    {
        $container->bindSingleton(LazyFooInterface::class, LazyConcreteFoo::class);
    }
}

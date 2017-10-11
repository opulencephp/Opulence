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
use Opulence\Ioc\IContainer;

/**
 * Mocks a lazy bootstrapper
 */
class EagerBootstrapper extends BaseBootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $container->bindSingleton(EagerFooInterface::class, EagerConcreteFoo::class);
    }
}

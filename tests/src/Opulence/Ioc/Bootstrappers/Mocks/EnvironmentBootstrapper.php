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
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

/**
 * Defines a bootstrapper that manipulates the environment
 */
class EnvironmentBootstrapper extends BaseBootstrapper implements ILazyBootstrapper
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
    public function registerBindings(IContainer $container)
    {
        $container->bindSingleton(LazyFooInterface::class, LazyConcreteFoo::class);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        putenv('TEST_ENV_NAME=running');
    }

    /**
     * @inheritdoc
     */
    public function shutdown()
    {
        putenv('TEST_ENV_NAME=shutdown');
    }
}

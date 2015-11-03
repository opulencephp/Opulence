<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Applications\Bootstrappers\Mocks;

use Opulence\Applications\Bootstrappers\Bootstrapper as BaseBootstrapper;
use Opulence\Applications\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

/**
 * Mocks a lazy bootstrapper
 */
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
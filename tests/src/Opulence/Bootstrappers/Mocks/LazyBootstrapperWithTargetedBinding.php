<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Bootstrappers\Mocks;

use Opulence\Bootstrappers\Bootstrapper as BaseBootstrapper;
use Opulence\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

/**
 * Mocks a lazy bootstrapper with a targeted binding
 */
class LazyBootstrapperWithTargetedBinding extends BaseBootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings()
    {
        return [
            [LazyFooInterface::class => EagerBootstrapperThatDependsOnBindingFromLazyBootstrapper::class]
        ];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $container->bind(
            LazyFooInterface::class,
            LazyConcreteFoo::class,
            EagerBootstrapperThatDependsOnBindingFromLazyBootstrapper::class
        );
    }
}
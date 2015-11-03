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
 * Defines a bootstrapper that depends on a binding from a lazy bootstrapper
 */
class LazyBootstrapperThatDependsOnBindingFromLazyBootstrapper extends BaseBootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings()
    {
        return [EagerFooInterface::class];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $container->bind(EagerFooInterface::class, EagerConcreteFoo::class);
    }

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
<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Bootstrappers\Mocks;

use Opulence\Bootstrappers\Bootstrapper as BaseBootstrapper;
use Opulence\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

/**
 * Defines a bootstrapper that does everything
 */
class BootstrapperWithEverything extends BaseBootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritDoc
     */
    public function getBindings()
    {
        return [LazyFooInterface::class];
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        echo "initialize";
    }

    /**
     * @inheritDoc
     */
    public function registerBindings(IContainer $container)
    {
        echo "registerBindings";
        $container->bind(LazyFooInterface::class, LazyConcreteFoo::class);
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        echo "run";
    }

    /**
     * @inheritDoc
     */
    public function shutdown()
    {
        echo "shutdown";
    }
}
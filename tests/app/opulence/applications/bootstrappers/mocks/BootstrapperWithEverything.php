<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a bootstrapper that does everything
 */
namespace Opulence\Tests\Applications\Bootstrappers\Mocks;

use Opulence\Applications\Bootstrappers\Bootstrapper as BaseBootstrapper;
use Opulence\Applications\Bootstrappers\ILazyBootstrapper;
use Opulence\IoC\IContainer;

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
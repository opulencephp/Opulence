<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a bootstrapper that manipulates the environment
 */
namespace RDev\Tests\Applications\Bootstrappers\Mocks;
use RDev\Applications\Bootstrappers\Bootstrapper;

class EnvironmentBootstrapper extends Bootstrapper
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->environment->setName("running");
    }

    /**
     * {@inheritdoc}
     */
    public function shutdown()
    {
        $this->environment->setName("shutting down");
    }
}
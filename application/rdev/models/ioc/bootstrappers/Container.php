<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the container bootstrapper
 */
namespace RDev\Models\IoC\Bootstrappers;
use RDev\Models\Applications\Bootstrappers;

class Container extends Bootstrappers\Bootstrapper
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $container = $this->application->getIoCContainer();
        $container->bind("RDev\\Models\\HTTP\\Connection", $this->application->getConnection());
        $container->bind("RDev\\Models\\HTTP\\Request", $this->application->getConnection()->getRequest());
        $container->bind("Monolog\\Logger", $this->application->getLogger());
        $container->bind("RDev\\Models\\Session\\ISession", $this->application->getSession());
    }
}
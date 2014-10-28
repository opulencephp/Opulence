<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the container bootstrapper
 */
namespace RDev\IoC\Bootstrappers;
use RDev\Applications\Bootstrappers;

class Container extends Bootstrappers\Bootstrapper
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $container = $this->application->getIoCContainer();
        $container->bind("RDev\\HTTP\\Connection", $this->application->getConnection());
        $container->bind("RDev\\HTTP\\Request", $this->application->getConnection()->getRequest());
        $container->bind("Monolog\\Logger", $this->application->getLogger());
        $container->bind("RDev\\Session\\ISession", $this->application->getSession());
    }
}
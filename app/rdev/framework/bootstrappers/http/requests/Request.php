<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the request bootstrapper
 */
namespace RDev\Framework\Bootstrappers\HTTP\Requests;
use RDev\Applications\Bootstrappers;
use RDev\HTTP\Requests;
use RDev\IoC;

class Request extends Bootstrappers\Bootstrapper
{
    /**
     * {@inheritdoc}
     */
    public function registerBindings(IoC\IContainer $container)
    {
        $container->bind("RDev\\HTTP\\Requests\\Request", Requests\Request::createFromGlobals());
    }
}
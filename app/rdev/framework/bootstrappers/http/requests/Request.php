<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the request bootstrapper
 */
namespace RDev\Framework\Bootstrappers\HTTP\Requests;
use RDev\Applications\Bootstrappers\Bootstrapper;
use RDev\HTTP\Requests\Request as HTTPRequest;
use RDev\IoC\IContainer;

class Request extends Bootstrapper
{
    /**
     * {@inheritdoc}
     */
    public function registerBindings(IContainer $container)
    {
        $container->bind("RDev\\HTTP\\Requests\\Request", HTTPRequest::createFromGlobals());
    }
}
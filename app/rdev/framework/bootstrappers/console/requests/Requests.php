<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the request bootstrapper
 */
namespace RDev\Framework\Bootstrappers\Console\Requests;
use RDev\Applications\Bootstrappers;
use RDev\Console\Requests\Parsers;
use RDev\IoC;

class Requests extends Bootstrappers\Bootstrapper
{
    /**
     * {@inheritdoc}
     */
    public function registerBindings(IoC\IContainer $container)
    {
        $container->bind("RDev\\Console\\Requests\\Parsers\\IParser", $this->getRequestParser($container));
    }

    /**
     * Gets the requests parser
     * To use a different request parser than the one returned here, extend this class and override this method
     *
     * @param IoC\IContainer $container The dependency injection container
     * @return Parsers\IParser The request parser
     */
    protected function getRequestParser(IoC\IContainer $container)
    {
        return new Parsers\Argv();
    }
}
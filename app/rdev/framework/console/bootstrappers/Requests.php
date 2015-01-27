<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the request bootstrapper
 */
namespace RDev\Framework\Console\Bootstrappers;
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
        $parser = new Parsers\Argv();
        $container->bind("RDev\\Console\\Requests\\Parsers\\IParser", $parser);
    }
}
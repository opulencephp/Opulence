<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the request bootstrapper
 */
namespace RDev\Framework\Bootstrappers\Console\Requests;
use RDev\Applications\Bootstrappers\Bootstrapper;
use RDev\Console\Requests\Parsers\ArgvParser;
use RDev\Console\Requests\Parsers\IParser;
use RDev\IoC\IContainer;

class Requests extends Bootstrapper
{
    /**
     * {@inheritdoc}
     */
    public function registerBindings(IContainer $container)
    {
        $container->bind(IParser::class, $this->getRequestParser($container));
    }

    /**
     * Gets the requests parser
     * To use a different request parser than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     * @return IParser The request parser
     */
    protected function getRequestParser(IContainer $container)
    {
        return new ArgvParser();
    }
}
<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the request bootstrapper
 */
namespace Opulence\Framework\Bootstrappers\Console\Requests;
use Opulence\Applications\Bootstrappers\Bootstrapper;
use Opulence\Console\Requests\Parsers\ArgvParser;
use Opulence\Console\Requests\Parsers\IParser;
use Opulence\IoC\IContainer;

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
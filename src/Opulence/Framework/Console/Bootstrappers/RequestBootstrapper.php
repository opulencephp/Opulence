<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Console\Bootstrappers;

use Opulence\Console\Requests\Parsers\ArgvParser;
use Opulence\Console\Requests\Parsers\IParser;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\IContainer;

/**
 * Defines the request bootstrapper
 */
class RequestBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $container->bindInstance(IParser::class, $this->getRequestParser($container));
    }

    /**
     * Gets the requests parser
     * To use a different request parser than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     * @return IParser The request parser
     */
    protected function getRequestParser(IContainer $container) : IParser
    {
        return new ArgvParser();
    }
}

<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Bootstrappers\Console\Requests;

use Opulence\Bootstrappers\Bootstrapper;
use Opulence\Console\Requests\Parsers\ArgvParser;
use Opulence\Console\Requests\Parsers\IParser;
use Opulence\Ioc\IContainer;

/**
 * Defines the request bootstrapper
 */
class RequestsBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
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
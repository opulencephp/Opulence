<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Bootstrappers\Http\Requests;

use Opulence\Bootstrappers\Bootstrapper;
use Opulence\Http\Requests\Request;
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
        $container->bind(Request::class, Request::createFromGlobals());
    }
}
<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Framework\Http\Bootstrappers;

use Opulence\Http\Requests\Request;
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
        $container->bindInstance(Request::class, Request::createFromGlobals());
    }
}

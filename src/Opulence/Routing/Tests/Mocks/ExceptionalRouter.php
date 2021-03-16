<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Routing\Tests\Mocks;

use Exception;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Router;

/**
 * Mocks a router that always throws an exception for use in testing
 */
class ExceptionalRouter extends Router
{
    /**
     * @inheritdoc
     */
    public function route(Request $request) : Response
    {
        throw new Exception('Foo');
    }
}

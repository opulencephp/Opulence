<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Routing\Mocks;

use Exception;
use Opulence\Http\Requests\Request;
use Opulence\Routing\Router;

/**
 * Mocks a router that always throws an exception for use in testing
 */
class ExceptionalRouter extends Router
{
    /**
     * @inheritdoc
     */
    public function route(Request $request)
    {
        throw new Exception("Foo");
    }
}
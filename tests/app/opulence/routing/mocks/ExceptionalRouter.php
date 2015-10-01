<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a router that always throws an exception for use in testing
 */
namespace Opulence\Tests\Routing\Mocks;

use Exception;
use Opulence\HTTP\Requests\Request;
use Opulence\Routing\Router;

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
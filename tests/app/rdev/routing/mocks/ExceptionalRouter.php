<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Mocks a router that always throws an exception for use in testing
 */
namespace RDev\Tests\Routing\Mocks;
use Exception;
use RDev\HTTP\Requests\Request;
use RDev\Routing\Router;

class ExceptionalRouter extends Router
{
    /**
     * {@inheritdoc}
     */
    public function route(Request $request)
    {
        throw new Exception("Foo");
    }
}
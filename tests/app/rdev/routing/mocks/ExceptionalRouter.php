<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Mocks a router that always throws an exception for use in testing
 */
namespace RDev\Tests\Routing\Mocks;
use RDev\HTTP;
use RDev\Routing;

class ExceptionalRouter extends Routing\Router
{
    /**
     * {@inheritdoc}
     */
    public function route(HTTP\Request $request)
    {
        throw new \Exception("Foo");
    }
}
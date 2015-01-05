<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Mocks a router that always throws an exception for use in testing
 */
namespace RDev\Tests\HTTP\Routing\Mocks;
use RDev\HTTP\Requests;
use RDev\HTTP\Routing;

class ExceptionalRouter extends Routing\Router
{
    /**
     * {@inheritdoc}
     */
    public function route(Requests\Request $request)
    {
        throw new \Exception("Foo");
    }
}
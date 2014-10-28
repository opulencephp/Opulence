<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks a filter that does not return something
 */
namespace RDev\Tests\Routing\Mocks;
use RDev\HTTP;
use RDev\Routing;
use RDev\Routing\Filters;

class DoesNotReturnSomethingFilter implements Filters\IFilter
{
    /**
     * {@inheritdoc}
     */
    public function run(Routing\Route $route, HTTP\Request $request, HTTP\Response $response = null)
    {
        // Don't do anything
    }
}
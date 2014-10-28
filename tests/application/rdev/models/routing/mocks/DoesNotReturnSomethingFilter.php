<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks a filter that does not return something
 */
namespace RDev\Tests\Models\Routing\Mocks;
use RDev\Models\HTTP;
use RDev\Models\Routing;
use RDev\Models\Routing\Filters;

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
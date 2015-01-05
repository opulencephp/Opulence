<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a filter that does not return something
 */
namespace RDev\Tests\Routing\Mocks;
use RDev\HTTP;
use RDev\HTTP\Routing\Filters;
use RDev\HTTP\Routing\Routes;

class DoesNotReturnSomethingFilter implements Filters\IFilter
{
    /**
     * {@inheritdoc}
     */
    public function run(Routes\CompiledRoute $route, HTTP\Request $request, HTTP\Response $response = null)
    {
        // Don't do anything
    }
}
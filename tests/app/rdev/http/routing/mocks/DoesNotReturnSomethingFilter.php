<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a filter that does not return something
 */
namespace RDev\Tests\HTTP\Routing\Mocks;
use RDev\HTTP\Requests;
use RDev\HTTP\Responses;
use RDev\HTTP\Routing\Filters;
use RDev\HTTP\Routing\Routes;

class DoesNotReturnSomethingFilter implements Filters\IFilter
{
    /**
     * {@inheritdoc}
     */
    public function run(Routes\CompiledRoute $route, Requests\Request $request, Responses\Response $response = null)
    {
        // Don't do anything
    }
}
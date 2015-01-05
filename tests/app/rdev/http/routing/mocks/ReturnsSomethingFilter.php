<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a filter that returns something
 */
namespace RDev\Tests\HTTP\Routing\Mocks;
use RDev\HTTP\Requests;
use RDev\HTTP\Responses;
use RDev\HTTP\Routing\Filters;
use RDev\HTTP\Routing\Routes;

class ReturnsSomethingFilter implements Filters\IFilter
{
    /**
     * {@inheritdoc}
     */
    public function run(Routes\CompiledRoute $route, Requests\Request $request, Responses\Response $response = null)
    {
        if($response !== null)
        {
            $response->setContent($response->getContent() . ":something");

            return $response;
        }
        else
        {
            return new Responses\RedirectResponse("/bar");
        }
    }
}
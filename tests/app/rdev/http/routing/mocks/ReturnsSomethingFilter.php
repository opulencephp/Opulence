<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a filter that returns something
 */
namespace RDev\Tests\Routing\Mocks;
use RDev\HTTP;
use RDev\HTTP\Routing\Filters;
use RDev\HTTP\Routing\Routes;

class ReturnsSomethingFilter implements Filters\IFilter
{
    /**
     * {@inheritdoc}
     */
    public function run(Routes\CompiledRoute $route, HTTP\Request $request, HTTP\Response $response = null)
    {
        if($response !== null)
        {
            $response->setContent($response->getContent() . ":something");

            return $response;
        }
        else
        {
            return new HTTP\RedirectResponse("/bar");
        }
    }
}
<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks a filter that returns something
 */
namespace RDev\Tests\Routing\Mocks;
use RDev\HTTP;
use RDev\Routing;
use RDev\Routing\Filters;

class ReturnsSomethingFilter implements Filters\IFilter
{
    /**
     * {@inheritdoc}
     */
    public function run(Routing\Route $route, HTTP\Request $request, HTTP\Response $response = null)
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
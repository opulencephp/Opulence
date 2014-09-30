<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the route compiler for use in testing
 */
namespace RDev\Tests\Models\Web\Routing\Mocks;
use RDev\Models\Web\Routing;
use RDev\Models\Web\Routing\Route;

class RouteCompiler implements Routing\IRouteCompiler
{
    /**
     * {@inheritdoc}
     */
    public function compile(Route &$route)
    {
        $route->setRegex("/^foo$/");
    }
} 
<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the route compiler for use in testing
 */
namespace RDev\Tests\Routing\Compilers\Mocks;
use RDev\Routing;
use RDev\Routing\Compilers;

class Compiler implements Compilers\ICompiler
{
    /**
     * {@inheritdoc}
     */
    public function compile(Routing\Route &$route)
    {
        $route->setPathRegex("/^foo$/");
    }
} 
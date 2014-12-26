<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the router for use in testing
 */
namespace RDev\Tests\Routing\Mocks;
use RDev\IoC;
use RDev\Routing;
use RDev\Routing\Compilers;
use RDev\Routing\Compilers\Parsers;
use RDev\Tests\Routing\Dispatchers\Mocks;

class Router extends Routing\Router
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $compiler = new Compilers\Compiler(new Parsers\Parser());

        parent::__construct(new Mocks\Dispatcher(new IoC\Container()), $compiler);
    }
} 
<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the router for use in testing
 */
namespace RDev\Tests\Routing\Mocks;
use RDev\IoC\Container;
use RDev\HTTP\Routing\Compilers\Compiler;
use RDev\HTTP\Routing\Compilers\Parsers\Parser;
use RDev\HTTP\Routing\Router as BaseRouter;
use RDev\Tests\Routing\Dispatchers\Mocks\Dispatcher;

class Router extends BaseRouter
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $compiler = new Compiler(new Parser());

        parent::__construct(new Dispatcher(new Container()), $compiler);
    }
} 
<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the router for use in testing
 */
namespace RDev\Tests\Routing\Mocks;
use RDev\IoC\Container;
use RDev\Routing\Compilers\Compiler;
use RDev\Routing\Compilers\Parsers\Parser;
use RDev\Routing\Router as BaseRouter;
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
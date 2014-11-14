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

class Router extends Routing\Router
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct(new Dispatcher(new IoC\Container()), new Compilers\Compiler());
    }
} 
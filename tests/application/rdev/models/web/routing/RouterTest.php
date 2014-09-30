<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the router
 */
namespace RDev\Models\Web\Routing;
use RDev\Models\Web;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    /** @var Router The router to use in tests */
    private $router = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $configArray = [
            "routes" => [
                new Route([Web\Request::METHOD_DELETE], "/foo", []),
                new Route([Web\Request::METHOD_GET], "/foo", []),
                new Route([Web\Request::METHOD_POST], "/foo", []),
                new Route([Web\Request::METHOD_PUT], "/foo", [])
            ]
        ];
        $this->router = new Router(new Web\HTTPConnection, $configArray);
    }

    public function testNothing()
    {
        $this->assertTrue(true);
    }
} 
<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the router config
 */
namespace RDev\Models\Web\Routing\Configs;
use RDev\Models\Web\Routing;
use RDev\Tests\Models\Web\Routing\Mocks;

class RouterConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests a controller without a controller specified
     */
    public function testControllerWithControllerMethod()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "routes" => [
                [
                    "methods" => ["GET", "POST"],
                    "path" => "/foo",
                    "options" => [
                        "controller" => "@bar"
                    ]
                ]
            ]
        ];
        new RouterConfig($configArray);
    }

    /**
     * Tests a controller without a method specified
     */
    public function testControllerWithNoMethod()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "routes" => [
                [
                    "methods" => ["GET", "POST"],
                    "path" => "/foo",
                    "options" => [
                        "controller" => "foo@"
                    ]
                ]
            ]
        ];
        new RouterConfig($configArray);
    }

    /**
     * Tests using an empty config
     */
    public function testEmptyConfig()
    {
        $config = new RouterConfig([]);
        $this->assertEquals([
            "compiler" => new Routing\RouteCompiler(),
            "routes" => []
        ], $config->toArray());
    }

    /**
     * Tests using an invalid compiler class
     */
    public function testInvalidCompilerClass()
    {
        $this->setExpectedException("\\RuntimeException");
        new RouterConfig([
            "compiler" => 123
        ]);
    }

    /**
     * Tests using an invalid compiler object
     */
    public function testInvalidCompilerObject()
    {
        $this->setExpectedException("\\RuntimeException");
        new RouterConfig([
            "compiler" => get_class($this)
        ]);
    }

    /**
     * Tests an improperly-formatted controller
     */
    public function testInvalidFormatController()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "routes" => [
                [
                    "methods" => ["GET", "POST"],
                    "path" => "/foo",
                    "options" => [
                        "controller" => "foo"
                    ]
                ]
            ]
        ];
        new RouterConfig($configArray);
    }

    /**
     * Tests using an invalid value for the routes
     */
    public function testInvalidRoutesValueType()
    {
        $this->setExpectedException("\\RuntimeException");
        new RouterConfig([
            "routes" => [
                "foo"
            ]
        ]);
    }

    /**
     * Tests not specifying a controller
     */
    public function testNotSpecifyingController()
    {
        $this->setExpectedException("\\RuntimeException");
        new RouterConfig([
            "routes" => [
                [
                    "methods" => "GET",
                    "path" => "/foo",
                    "options" => []
                ]
            ]
        ]);
    }

    /**
     * Tests not specifying a method
     */
    public function testNotSpecifyingMethod()
    {
        $this->setExpectedException("\\RuntimeException");
        new RouterConfig([
            "routes" => [
                [
                    "path" => "/foo"
                ]
            ]
        ]);
    }

    /**
     * Tests not specifying a path
     */
    public function testNotSpecifyingPath()
    {
        $this->setExpectedException("\\RuntimeException");
        new RouterConfig([
            "routes" => [
                [
                    "methods" => "GET"
                ]
            ]
        ]);
    }

    /**
     * Tests specifying after-filters
     */
    public function testSpecifyingAfterFilters()
    {
        $configArray = [
            "routes" => [
                [
                    "methods" => "GET",
                    "path" => "/foo",
                    "options" => [
                        "controller" => "foo@bar",
                        "after" => "foo"
                    ]
                ]
            ]
        ];
        $config = new RouterConfig($configArray);
        /** @var Routing\Route $route */
        $route = $config->toArray()["routes"][0];
        $this->assertEquals(["foo"], $route->getAfterFilters());
    }

    /**
     * Tests specifying before-filters
     */
    public function testSpecifyingBeforeFilters()
    {
        $configArray = [
            "routes" => [
                [
                    "methods" => "GET",
                    "path" => "/foo",
                    "options" => [
                        "controller" => "foo@bar",
                        "before" => "foo"
                    ]
                ]
            ]
        ];
        $config = new RouterConfig($configArray);
        /** @var Routing\Route $route */
        $route = $config->toArray()["routes"][0];
        $this->assertEquals(["foo"], $route->getBeforeFilters());
    }

    /**
     * Tests specifying a compiler class
     */
    public function testSpecifyingCompilerClass()
    {
        $compiler = "RDev\\Tests\\Models\\Web\\Routing\\Mocks\\RouteCompiler";
        $configArray = [
            "compiler" => $compiler
        ];
        $config = new RouterConfig($configArray);
        $this->assertEquals([
            "compiler" => new Mocks\RouteCompiler(),
            "routes" => []
        ], $config->toArray());
    }

    /**
     * Tests specifying a compiler object
     */
    public function testSpecifyingCompilerObject()
    {
        $compiler = new Routing\RouteCompiler();
        $configArray = [
            "compiler" => $compiler
        ];
        $config = new RouterConfig($configArray);
        $this->assertEquals([
            "compiler" => $compiler,
            "routes" => []
        ], $config->toArray());
        $this->assertSame($compiler, $config->toArray()["compiler"]);
    }

    /**
     * Tests specifying a route array with multiple methods
     */
    public function testSpecifyingRouteArrayWithMultipleMethods()
    {
        $configArray = [
            "routes" => [
                [
                    "methods" => ["GET", "POST"],
                    "path" => "/foo",
                    "options" => [
                        "controller" => "foo@bar"
                    ]
                ]
            ]
        ];
        $config = new RouterConfig($configArray);
        /** @var Routing\Route $route */
        $route = $config["routes"][0];
        $this->assertEquals(["GET", "POST"], $route->getMethods());
        $this->assertEquals("/foo", $route->getRawPath());
    }

    /**
     * Tests specifying a route array with options
     */
    public function testSpecifyingRouteArrayWithOptions()
    {
        $configArray = [
            "routes" => [
                [
                    "methods" => "GET",
                    "path" => "/foo",
                    "options" => [
                        "controller" => "foo@bar"
                    ]
                ]
            ]
        ];
        $config = new RouterConfig($configArray);
        /** @var Routing\Route $route */
        $route = $config["routes"][0];
        $this->assertEquals(["GET"], $route->getMethods());
        $this->assertEquals("/foo", $route->getRawPath());
    }

    /**
     * Tests specifying a route object
     */
    public function testSpecifyingRouteObject()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Routing\Route(["get"], "/foo", $options);
        $configArray = [
            "routes" => [$route]
        ];
        $config = new RouterConfig($configArray);
        $this->assertSame($route, $config["routes"][0]);
    }
} 
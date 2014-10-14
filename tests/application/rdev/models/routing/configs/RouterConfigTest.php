<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the router config
 */
namespace RDev\Models\Routing\Configs;
use RDev\Models\Routing;
use RDev\Tests\Models\Routing\Mocks;

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
            "groups" => [],
            "routes" => []
        ], $config->toArray());
    }

    /**
     * Tests grouped routes
     */
    public function testGroups()
    {
        $configArray = [
            "groups" => [
                [
                    "options" => [
                        "pre" => ["pre1", "pre2"],
                        "post" => ["post1", "post2"],
                        "controllerNamespace" => "MyApp\\Controllers",
                        "path" => "/my/path"
                    ],
                    "routes" => [
                        [
                            "methods" => "GET",
                            "path" => "/foo",
                            "options" => [
                                "controller" => "foo@bar"
                            ]
                        ],
                        [
                            "methods" => "POST",
                            "path" => "/bar",
                            "options" => [
                                "controller" => "foo@blah"
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $config = new RouterConfig($configArray);
        /** @var Routing\Route $getRoute */
        $getRoute = $config["groups"][0]["routes"][0];
        /** @var Routing\Route $postRoute */
        $postRoute = $config["groups"][0]["routes"][1];
        $this->assertInstanceOf("RDev\\Models\\Routing\\Route", $getRoute);
        $this->assertInstanceOf("RDev\\Models\\Routing\\Route", $postRoute);
        $this->assertEquals(["GET"], $getRoute->getMethods());
        $this->assertEquals(["POST"], $postRoute->getMethods());
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
     * Tests specifying an invalid type for group options
     */
    public function testInvalidGroupOptionsType()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "groups" => [
                [
                    "options" => 1,
                    "routes" => [
                        [
                            "methods" => "GET",
                            "path" => "/foo",
                            "options" => [
                                "controller" => "foo@bar"
                            ]
                        ]
                    ]
                ]
            ]
        ];
        new RouterConfig($configArray);
    }

    /**
     * Tests specifying an invalid type for group routes
     */
    public function testInvalidGroupRoutesType()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "groups" => [
                [
                    "options" => [],
                    "routes" => 1
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
     * Tests nested grouped routes
     */
    public function testNestedGroups()
    {
        $configArray = [
            "groups" => [
                [
                    "options" => [
                        "pre" => ["pre1", "pre2"],
                        "post" => ["post1", "post2"],
                        "controllerNamespace" => "MyApp\\Controllers",
                        "path" => "/my/path"
                    ],
                    "groups" => [
                        [
                            "options" => [
                                "pre" => ["pre3", "pre4"],
                                "post" => ["post3", "post4"],
                                "controllerNamespace" => "Users",
                                "path" => "/users/{userId}/profile"
                            ],
                            "routes" => [
                                [
                                    "methods" => "GET",
                                    "path" => "",
                                    "options" => [
                                        "controller" => "UserController@showProfile"
                                    ]
                                ],
                                [
                                    "methods" => "POST",
                                    "path" => "/update",
                                    "options" => [
                                        "controller" => "UserController@editProfile"
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "routes" => [
                        [
                            "methods" => "GET",
                            "path" => "/foo",
                            "options" => [
                                "controller" => "foo@bar"
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $config = new RouterConfig($configArray);
        /** @var Routing\Route[] $outsideGroupRoutes */
        $outsideGroupRoutes = $config["groups"][0]["routes"];
        /** @var Routing\Route[] $nestedGroupRoutes */
        $nestedGroupRoutes = $config["groups"][0]["groups"][0]["routes"];
        $this->assertInstanceOf("RDev\\Models\\Routing\\Route", $outsideGroupRoutes[0]);
        $this->assertInstanceOf("RDev\\Models\\Routing\\Route", $nestedGroupRoutes[0]);
        $this->assertEquals("", $nestedGroupRoutes[0]->getRawPath());
        $this->assertInstanceOf("RDev\\Models\\Routing\\Route", $nestedGroupRoutes[1]);
        $this->assertEquals("/update", $nestedGroupRoutes[1]->getRawPath());
    }

    /**
     * Tests nested groups without routes
     */
    public function testNestedGroupsWithoutRoutes()
    {
        $configArray = [
            "groups" => [
                [
                    "options" => [
                        "path" => "/foo",
                        "controllerNamespace" => "MyApp"
                    ],
                    "groups" => [
                        [
                            "options" => [
                                "path" => "/bar",
                                "controllerNamespace" => "Controllers"
                            ],
                            "routes" => [
                                [
                                    "methods" => "GET",
                                    "path" => "/blah",
                                    "options" => [
                                        "controller" => "MyController@myMethod"
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $config = new RouterConfig($configArray);
        /** @var Routing\Route $getRoute */
        $getRoute = $config["groups"][0]["groups"][0]["routes"][0];
        $this->assertEquals(["GET"], $getRoute->getMethods());
        $this->assertEquals("/blah", $getRoute->getRawPath());
        $this->assertEquals("MyController", $getRoute->getControllerName());
        $this->assertEquals("myMethod", $getRoute->getControllerMethod());
    }

    /**
     * Tests not setting any options in a group
     */
    public function testNotSettingOptionsInGroup()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "groups" => [
                [
                    "routes" => [
                        [
                            "methods" => "GET",
                            "path" => "/foo",
                            "options" => [
                                "controller" => "foo@bar"
                            ]
                        ]
                    ]
                ]
            ]
        ];
        new RouterConfig($configArray);
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
     * Tests specifying a compiler class
     */
    public function testSpecifyingCompilerClass()
    {
        $compiler = "RDev\\Tests\\Models\\Routing\\Mocks\\RouteCompiler";
        $configArray = [
            "compiler" => $compiler
        ];
        $config = new RouterConfig($configArray);
        $this->assertEquals([
            "compiler" => new Mocks\RouteCompiler(),
            "routes" => [],
            "groups" => []
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
            "routes" => [],
            "groups" => []
        ], $config->toArray());
        $this->assertSame($compiler, $config->toArray()["compiler"]);
    }

    /**
     * Tests specifying post-filters
     */
    public function testSpecifyingPostFilters()
    {
        $configArray = [
            "routes" => [
                [
                    "methods" => "GET",
                    "path" => "/foo",
                    "options" => [
                        "controller" => "foo@bar",
                        "post" => "foo"
                    ]
                ]
            ]
        ];
        $config = new RouterConfig($configArray);
        /** @var Routing\Route $route */
        $route = $config->toArray()["routes"][0];
        $this->assertEquals(["foo"], $route->getPostFilters());
    }

    /**
     * Tests specifying pre-filters
     */
    public function testSpecifyingPreFilters()
    {
        $configArray = [
            "routes" => [
                [
                    "methods" => "GET",
                    "path" => "/foo",
                    "options" => [
                        "controller" => "foo@bar",
                        "pre" => "foo"
                    ]
                ]
            ]
        ];
        $config = new RouterConfig($configArray);
        /** @var Routing\Route $route */
        $route = $config->toArray()["routes"][0];
        $this->assertEquals(["foo"], $route->getPreFilters());
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
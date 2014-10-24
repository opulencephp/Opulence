<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the router factory
 */
namespace RDev\Models\Routing\Factories;
use RDev\Models\IoC;
use RDev\Models\Routing;
use RDev\Models\Routing\Configs;

class RouterFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var RouterFactory The factory to use to create routers */
    private $routerFactory = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->routerFactory = new RouterFactory();
    }

    /**
     * Tests using groups in the config
     */
    public function testGroupsInConfig()
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
            ],
            "groups" => [
                [
                    "options" => [
                        "pre" => ["pre1", "pre2"],
                        "post" => ["post1", "post2"],
                        "controllerNamespace" => "MyApp\\Controllers",
                        "path" => "/group"
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
                                "controller" => "foo@bar"
                            ]
                        ]
                    ],
                    "groups" => [
                        [
                            "options" => [
                                "pre" => ["pre3", "pre4"],
                                "post" => ["post3", "post4"],
                                "controllerNamespace" => "User",
                                "path" => "/user"
                            ],
                            "routes" => [
                                [
                                    "methods" => "GET",
                                    "path" => "/{userId}",
                                    "options" => [
                                        "controller" => "UserController@showUser"
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $config = new Configs\RouterConfig($configArray);
        $container = new IoC\Container();
        $router = $this->routerFactory->createFromConfig($config, $container);
        /** @var Routing\Route[] $getRoutes */
        $getRoutes = $router->getRoutes("GET");
        /** @var Routing\Route[] $postRoutes */
        $postRoutes = $router->getRoutes("POST");
        // Test first GET route
        $this->assertEquals("/group/foo", $getRoutes[0]->getRawPath());
        $this->assertEquals("MyApp\\Controllers\\foo", $getRoutes[0]->getControllerName());
        $this->assertEquals(["pre1", "pre2"], $getRoutes[0]->getPreFilters());
        $this->assertEquals(["post1", "post2"], $getRoutes[0]->getPostFilters());
        // Test second GET route
        $this->assertEquals("/group/user/{userId}", $getRoutes[1]->getRawPath());
        $this->assertEquals("MyApp\\Controllers\\User\\UserController", $getRoutes[1]->getControllerName());
        $this->assertEquals(["pre1", "pre2", "pre3", "pre4"], $getRoutes[1]->getPreFilters());
        $this->assertEquals(["post1", "post2", "post3", "post4"], $getRoutes[1]->getPostFilters());
        // Test POST route
        $this->assertEquals("/group/bar", $postRoutes[0]->getRawPath());
        $this->assertEquals("MyApp\\Controllers\\foo", $postRoutes[0]->getControllerName());
        $this->assertEquals(["pre1", "pre2"], $postRoutes[0]->getPreFilters());
        $this->assertEquals(["post1", "post2"], $postRoutes[0]->getPostFilters());
        // Test non-grouped route
        $this->assertEquals([], $getRoutes[2]->getPreFilters());
        $this->assertEquals([], $getRoutes[2]->getPostFilters());
        $this->assertEquals("/foo", $getRoutes[2]->getRawPath());
        $this->assertEquals("foo", $getRoutes[2]->getControllerName());
    }

    /**
     * Tests passing in routes from the config
     */
    public function testPassingInRoutesFromConfig()
    {
        $getRoute = new Routing\Route("GET", "/foo", ["controller" => "MyController@myMethod"]);
        $configArray = [
            "routes" => [
                $getRoute
            ]
        ];
        $config = new Configs\RouterConfig($configArray);
        $container = new IoC\Container();
        $router = $this->routerFactory->createFromConfig($config, $container);
        $this->assertSame($getRoute, $router->getRoutes("GET")[0]);
    }
}
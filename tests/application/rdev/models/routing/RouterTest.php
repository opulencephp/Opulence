<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the router
 */
namespace RDev\Models\Routing;
use RDev\Models\HTTP;
use RDev\Models\IoC;
use RDev\Models\Routing\Configs;
use RDev\Tests\Models\Routing\Mocks;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    /** @var Router The router to use in tests */
    private $router = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->router = new Router(new IoC\Container(), new HTTP\Connection());
    }

    /**
     * Tests getting an invalid method's routes
     */
    public function testGettingInvalidMethodRoutes()
    {
        $path = "/foo";
        $options = [
            "controller" => "RDev\\Tests\\Controllers\\Mocks\\Controller@noParameters"
        ];
        $this->router->delete($path, $options);
        $this->router->get($path, $options);
        $this->router->post($path, $options);
        $this->router->put($path, $options);
        $this->assertEquals([], $this->router->getRoutes("methodThatDoeNotExist"));
    }

    /**
     * Tests getting the routes
     */
    public function testGettingRoutes()
    {
        $path = "/foo";
        $options = [
            "controller" => "RDev\\Tests\\Controllers\\Mocks\\Controller@noParameters"
        ];
        $deleteRoute = new Route(HTTP\Request::METHOD_DELETE, $path, $options);
        $getRoute = new Route(HTTP\Request::METHOD_GET, $path, $options);
        $postRoute = new Route(HTTP\Request::METHOD_POST, $path, $options);
        $putRoute = new Route(HTTP\Request::METHOD_PUT, $path, $options);
        $this->router->addRoute($deleteRoute);
        $this->router->addRoute($getRoute);
        $this->router->addRoute($postRoute);
        $this->router->addRoute($putRoute);
        $allRoutes = $this->router->getRoutes();
        $this->assertSame([$deleteRoute], $allRoutes[HTTP\Request::METHOD_DELETE]);
        $this->assertSame([$getRoute], $allRoutes[HTTP\Request::METHOD_GET]);
        $this->assertSame([$postRoute], $allRoutes[HTTP\Request::METHOD_POST]);
        $this->assertSame([$putRoute], $allRoutes[HTTP\Request::METHOD_PUT]);
    }

    /**
     * Tests getting a specific method's routes
     */
    public function testGettingSpecificMethodRoutes()
    {
        $path = "/foo";
        $options = [
            "controller" => "RDev\\Tests\\Controllers\\Mocks\\Controller@noParameters"
        ];
        $getRoute = new Route(HTTP\Request::METHOD_GET, $path, $options);
        $this->router->addRoute($getRoute);
        $getRoutes = $this->router->getRoutes(HTTP\Request::METHOD_GET);
        $this->assertSame([$getRoute], $getRoutes);
    }

    /**
     * Tests grouping routes
     */
    public function testGroupingRoutes()
    {
        $groupOptions = [
            "path" => "/foo",
            "pre" => ["pre1", "pre2"],
            "post" => ["post1", "post2"]
        ];
        $this->router->group($groupOptions, function ()
        {
            $routeOptions = ["controller" => "RDev\\Tests\\Controllers\\Mocks\\Controller@noParameters"];
            $this->router->addRoute(new Route(HTTP\Request::METHOD_GET, "/bar", $routeOptions));
            $this->router->delete("/blah", $routeOptions);
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRoutes(HTTP\Request::METHOD_GET);
        /** @var Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRoutes(HTTP\Request::METHOD_DELETE);
        $this->assertEquals("/foo/bar", $getRoutes[0]->getRawPath());
        $this->assertEquals(["pre1", "pre2"], $getRoutes[0]->getPreFilters());
        $this->assertEquals(["post1", "post2"], $getRoutes[0]->getPostFilters());
        $this->assertEquals("/foo/blah", $deleteRoutes[0]->getRawPath());
        $this->assertEquals(["pre1", "pre2"], $deleteRoutes[0]->getPreFilters());
        $this->assertEquals(["post1", "post2"], $deleteRoutes[0]->getPostFilters());
    }

    /**
     * Tests grouping the routes and then adding another route
     */
    public function testGroupingRoutesThenAddingAnotherRoute()
    {
        $routeOptions = [
            "controller" => "RDev\\Tests\\Controllers\\Mocks\\Controller@noParameters",
            "pre" => ["pre3", "pre4"],
            "post" => ["post3", "post4"]
        ];
        $groupOptions = [
            "path" => "/foo",
            "pre" => ["pre1", "pre2"],
            "post" => ["post1", "post2"]
        ];
        $this->router->group($groupOptions, function () use ($routeOptions)
        {
            $this->router->addRoute(new Route(HTTP\Request::METHOD_GET, "/bar", $routeOptions));
            $this->router->delete("/blah", $routeOptions);
        });
        $this->router->get("/asdf", $routeOptions);
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRoutes(HTTP\Request::METHOD_GET);
        /** @var Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRoutes(HTTP\Request::METHOD_DELETE);
        $this->assertEquals("/foo/bar", $getRoutes[0]->getRawPath());
        $this->assertEquals(["pre1", "pre2", "pre3", "pre4"], $getRoutes[0]->getPreFilters());
        $this->assertEquals(["post1", "post2", "post3", "post4"], $getRoutes[0]->getPostFilters());
        $this->assertEquals("/asdf", $getRoutes[1]->getRawPath());
        $this->assertEquals(["pre3", "pre4"], $getRoutes[1]->getPreFilters());
        $this->assertEquals(["post3", "post4"], $getRoutes[1]->getPostFilters());
        $this->assertEquals("/foo/blah", $deleteRoutes[0]->getRawPath());
        $this->assertEquals(["pre1", "pre2", "pre3", "pre4"], $deleteRoutes[0]->getPreFilters());
        $this->assertEquals(["post1", "post2", "post3", "post4"], $deleteRoutes[0]->getPostFilters());
    }

    /**
     * Tests grouping routes that use a string for the pre- and post-filters
     */
    public function testGroupingRoutesWithStringFilters()
    {
        $routeOptions = [
            "controller" => "RDev\\Tests\\Controllers\\Mocks\\Controller@noParameters",
            "pre" => "pre2",
            "post" => "post2"
        ];
        $groupOptions = [
            "path" => "/foo",
            "pre" => "pre1",
            "post" => "post1"
        ];
        $this->router->group($groupOptions, function () use ($routeOptions)
        {
            $this->router->addRoute(new Route(HTTP\Request::METHOD_GET, "/bar", $routeOptions));
            $this->router->delete("/blah", $routeOptions);
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRoutes(HTTP\Request::METHOD_GET);
        /** @var Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRoutes(HTTP\Request::METHOD_DELETE);
        $this->assertEquals(["pre1", "pre2"], $getRoutes[0]->getPreFilters());
        $this->assertEquals(["post1", "post2"], $getRoutes[0]->getPostFilters());
        $this->assertEquals(["pre1", "pre2"], $deleteRoutes[0]->getPreFilters());
        $this->assertEquals(["post1", "post2"], $deleteRoutes[0]->getPostFilters());
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
        $router = new Router(new IoC\Container(), new HTTP\Connection(), new Configs\RouterConfig($configArray));
        /** @var Route[] $getRoutes */
        $getRoutes = $router->getRoutes("GET");
        /** @var Route[] $postRoutes */
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
     * Tests nested grouped routes
     */
    public function testNestedGroupedRoutes()
    {
        $outerGroupOptions = [
            "path" => "/foo",
            "controllerNamespace" => "RDev\\Tests\\Controllers",
            "pre" => ["pre1", "pre2"],
            "post" => ["post1", "post2"]
        ];
        $outerRouteOptions = ["controller" => "Mocks\\Controller@noParameters"];
        $innerRouteOptions = ["controller" => "Controller@noParameters"];
        $this->router->group($outerGroupOptions, function () use ($outerRouteOptions, $innerRouteOptions)
        {
            $this->router->addRoute(new Route(HTTP\Request::METHOD_GET, "/bar", $outerRouteOptions));
            $this->router->delete("/blah", $outerRouteOptions);
            $innerGroupOptions = [
                "path" => "/asdf",
                "controllerNamespace" => "Mocks",
                "pre" => ["pre3", "pre4"],
                "post" => ["post3", "post4"]
            ];
            $this->router->group($innerGroupOptions, function () use ($innerRouteOptions)
            {
                $this->router->get("/jkl", $innerRouteOptions);
            });
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRoutes(HTTP\Request::METHOD_GET);
        /** @var Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRoutes(HTTP\Request::METHOD_DELETE);
        $this->assertEquals("/foo/bar", $getRoutes[0]->getRawPath());
        $this->assertEquals("RDev\\Tests\\Controllers\\Mocks\\Controller", $getRoutes[0]->getControllerName());
        $this->assertEquals(["pre1", "pre2"], $getRoutes[0]->getPreFilters());
        $this->assertEquals(["post1", "post2"], $getRoutes[0]->getPostFilters());
        $this->assertEquals("/foo/asdf/jkl", $getRoutes[1]->getRawPath());
        $this->assertEquals("RDev\\Tests\\Controllers\\Mocks\\Controller", $getRoutes[1]->getControllerName());
        $this->assertEquals(["pre1", "pre2", "pre3", "pre4"], $getRoutes[1]->getPreFilters());
        $this->assertEquals(["post1", "post2", "post3", "post4"], $getRoutes[1]->getPostFilters());
        $this->assertEquals("/foo/blah", $deleteRoutes[0]->getRawPath());
        $this->assertEquals("RDev\\Tests\\Controllers\\Mocks\\Controller", $deleteRoutes[0]->getControllerName());
        $this->assertEquals(["pre1", "pre2"], $deleteRoutes[0]->getPreFilters());
        $this->assertEquals(["post1", "post2"], $deleteRoutes[0]->getPostFilters());
    }

    /**
     * Tests passing in routes from the config
     */
    public function testPassingInRoutesFromConfig()
    {
        $getRoute = new Route("GET", "/foo", ["controller" => "MyController@myMethod"]);
        $config = [
            "routes" => [
                $getRoute
            ]
        ];
        $router = new Router(new IoC\Container(), new HTTP\Connection(), $config);
        $this->assertSame($getRoute, $router->getRoutes("GET")[0]);
    }

    /**
     * Tests routing for any method
     */
    public function testRoutingAnyMethod()
    {
        $options = [
            "controller" => "RDev\\Tests\\Controllers\\Mocks\\Controller@noParameters"
        ];
        $this->router->any("/foo", $options);
        $allRoutes = $this->router->getRoutes();
        $this->assertEquals(1, count($allRoutes[HTTP\Request::METHOD_GET]));
        $this->assertEquals(1, count($allRoutes[HTTP\Request::METHOD_POST]));
        $this->assertEquals(1, count($allRoutes[HTTP\Request::METHOD_DELETE]));
        $this->assertEquals(1, count($allRoutes[HTTP\Request::METHOD_PUT]));
    }

    /**
     * Tests routing a DELETE request
     */
    public function testRoutingDeleteRequest()
    {
        $this->doTestForHTTPMethod("DELETE");
    }

    /**
     * Tests routing a GET request
     */
    public function testRoutingGetRequest()
    {
        $this->doTestForHTTPMethod("GET");
    }

    /**
     * Tests routing for multiple methods
     */
    public function testRoutingMultipleMethods()
    {
        $options = [
            "controller" => "RDev\\Tests\\Controllers\\Mocks\\Controller@noParameters"
        ];
        $this->router->multiple([HTTP\Request::METHOD_GET, HTTP\Request::METHOD_POST], "/foo", $options);
        $allRoutes = $this->router->getRoutes();
        $this->assertEquals(1, count($allRoutes[HTTP\Request::METHOD_GET]));
        $this->assertEquals(1, count($allRoutes[HTTP\Request::METHOD_POST]));
        $this->assertEquals(0, count($allRoutes[HTTP\Request::METHOD_DELETE]));
        $this->assertEquals(0, count($allRoutes[HTTP\Request::METHOD_PUT]));
    }

    /**
     * Tests routing a POST request
     */
    public function testRoutingPostRequest()
    {
        $this->doTestForHTTPMethod("POST");
    }

    /**
     * Tests routing a PUT request
     */
    public function testRoutingPutRequest()
    {
        $this->doTestForHTTPMethod("PUT");
    }

    /**
     * Tests routing a route with an optional variable
     */
    public function testRoutingRouteWithOptionalVariable()
    {
        $options = [
            "controller" => "RDev\\Tests\\Controllers\\Mocks\\Controller@noParameters"
        ];
        $this->router->get("/foo/{bar?}", $options);
        $this->assertEquals("noParameters", $this->router->route("/foo/"));
    }

    /**
     * Tests routing a route with an optional variable with a default value
     */
    public function testRoutingRouteWithOptionalVariableWithDefaultValue()
    {
        $options = [
            "controller" => "RDev\\Tests\\Controllers\\Mocks\\Controller@oneParameter"
        ];
        $this->router->get("/bar/{foo?=23}", $options);
        $this->assertEquals("foo:23", $this->router->route("/bar/"));
    }

    /**
     * Tests specifying a namespace prefix
     */
    public function testSpecifyingNamespacePrefix()
    {
        $this->router->group(["controllerNamespace" => "MyApp\\Controllers\\"], function ()
        {
            $this->router->get("/foo", ["controller" => "ControllerA@myMethod"]);
            $this->router->post("/foo", ["controller" => "ControllerB@myMethod"]);
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRoutes("GET");
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRoutes("POST");
        $this->assertEquals("MyApp\\Controllers\\ControllerA", $getRoutes[0]->getControllerName());
        $this->assertEquals("MyApp\\Controllers\\ControllerB", $postRoutes[0]->getControllerName());
    }

    /**
     * Tests specifying a namespace prefix with no trailing slash
     */
    public function testSpecifyingNamespacePrefixWithNoTrailingSlash()
    {
        $this->router->group(["controllerNamespace" => "MyApp\\Controllers"], function ()
        {
            $this->router->get("/foo", ["controller" => "ControllerA@myMethod"]);
            $this->router->post("/foo", ["controller" => "ControllerB@myMethod"]);
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRoutes("GET");
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRoutes("POST");
        $this->assertEquals("MyApp\\Controllers\\ControllerA", $getRoutes[0]->getControllerName());
        $this->assertEquals("MyApp\\Controllers\\ControllerB", $postRoutes[0]->getControllerName());
    }

    /**
     * Sets up a router and does the routing and testing
     *
     * @param string $httpMethod The HTTP method to simulate in the call
     * @param string $rawPath The raw path the routes should use
     * @param string $pathToRoute The path to route
     * @param string $controllerName The name of the controller to call
     * @param string $controllerMethod The name of the method in the mock controller to call
     */
    private function doRoute($httpMethod, $rawPath, $pathToRoute, $controllerName, $controllerMethod)
    {
        $options = [
            "controller" => "$controllerName@$controllerMethod"
        ];

        // The mock router will return the route used rather than the output of the route controller
        // This makes testing easier
        $mockRouter = new Mocks\Router(new IoC\Container(), new HTTP\Connection());
        $deleteRoute = new Route(HTTP\Request::METHOD_DELETE, $rawPath, $options);
        $getRoute = new Route(HTTP\Request::METHOD_GET, $rawPath, $options);
        $postRoute = new Route(HTTP\Request::METHOD_POST, $rawPath, $options);
        $putRoute = new Route(HTTP\Request::METHOD_PUT, $rawPath, $options);
        $mockRouter->addRoute($deleteRoute);
        $mockRouter->addRoute($getRoute);
        $mockRouter->addRoute($postRoute);
        $mockRouter->addRoute($putRoute);
        $mockRouter->setHTTPMethod($httpMethod);

        switch($httpMethod)
        {
            case HTTP\Request::METHOD_DELETE:
                $this->assertSame($deleteRoute, $mockRouter->route($pathToRoute));
                break;
            case HTTP\Request::METHOD_GET:
                $this->assertSame($getRoute, $mockRouter->route($pathToRoute));
                break;
            case HTTP\Request::METHOD_POST:
                $this->assertSame($postRoute, $mockRouter->route($pathToRoute));
                break;
            case HTTP\Request::METHOD_PUT:
                $this->assertSame($putRoute, $mockRouter->route($pathToRoute));
                break;
        }
    }

    /**
     * Tests a request with the input HTTP method
     *
     * @param string $httpMethod The HTTP method to test
     */
    private function doTestForHTTPMethod($httpMethod)
    {
        $this->doRoute($httpMethod, "/foo", "/foo", "RDev\\Tests\\Controllers\\Mocks\\Controller", "noParameters");
        $this->doRoute($httpMethod, "/foo/{foo}", "/foo/123", "RDev\\Tests\\Controllers\\Mocks\\Controller",
            "oneParameter");
        $this->doRoute($httpMethod, "/foo/{foo}/{bar}", "/foo/123/456", "RDev\\Tests\\Controllers\\Mocks\\Controller",
            "multipleParameters");
    }
} 
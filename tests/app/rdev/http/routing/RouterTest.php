<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the router
 */
namespace RDev\HTTP\Routing;
use RDev\HTTP\Requests\Request;
use RDev\HTTP\Responses\ResponseHeaders;
use RDev\HTTP\Routing\Compilers\Compiler;
use RDev\HTTP\Routing\Compilers\Parsers\Parser;
use RDev\HTTP\Routing\Dispatchers\Dispatcher;
use RDev\HTTP\Routing\Routes\Route;
use RDev\HTTP\Routing\Routes\RouteCollection;
use RDev\IoC\Container;
use RDev\Tests\Routing\Mocks\Router as MockRouter;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    /** @var Router The router to use in tests */
    private $router = null;
    /** @var Compiler The compiler to use */
    private $compiler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $container = new Container();
        $this->compiler = new Compiler(new Parser());
        $this->router = new Router(new Dispatcher($container), $this->compiler);
    }

    /**
     * Tests adding routes through their specific methods
     */
    public function testAddingRoutesThroughTheirSpecificMethods()
    {
        $path = "/foo";
        $options = [
            "controller" => "foo@bar"
        ];

        foreach(RouteCollection::getMethods() as $method)
        {
            call_user_func_array([$this->router, strtolower($method)], [$path, $options]);
            $expectedRoute = new Route($method, $path, $options);
            $this->assertEquals([$expectedRoute], $this->router->getRouteCollection()->get($method));
        }
    }

    /**
     * Tests getting the matched route when there is none
     */
    public function testGettingMatchedRouteWhenThereIsNone()
    {
        $this->assertNull($this->router->getMatchedRoute());
    }

    /**
     * Tests a group with variable regexes
     */
    public function testGroupWithVariableRegexes()
    {
        $this->router->group(["path" => "/users/{userId}", "variables" => ["id" => "\d+"]], function ()
        {
            $this->router->get("/foo", ["controller" => "foo@bar"]);
            $this->router->post("/foo", ["controller" => "foo@bar"]);
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(Request::METHOD_GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(Request::METHOD_POST);
        $this->assertEquals("\d+", $getRoutes[0]->getVariableRegex("id"));
        $this->assertEquals("\d+", $postRoutes[0]->getVariableRegex("id"));
    }

    /**
     * Tests grouping routes
     */
    public function testGroupingRoutes()
    {
        $groupOptions = [
            "path" => "/foo",
            "middleware" => ["foo1", "foo2"]
        ];
        $this->router->group($groupOptions, function ()
        {
            $routeOptions = ["controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@noParameters"];
            $this->router->addRoute(new Route(Request::METHOD_GET, "/bar", $routeOptions));
            $this->router->delete("/blah", $routeOptions);
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(Request::METHOD_GET);
        /** @var Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRouteCollection()->get(Request::METHOD_DELETE);
        $this->assertEquals("/foo/bar", $getRoutes[0]->getRawPath());
        $this->assertEquals(["foo1", "foo2"], $getRoutes[0]->getMiddleware());
        $this->assertEquals("/foo/blah", $deleteRoutes[0]->getRawPath());
        $this->assertEquals(["foo1", "foo2"], $deleteRoutes[0]->getMiddleware());
    }

    /**
     * Tests grouping the routes and then adding another route
     */
    public function testGroupingRoutesThenAddingAnotherRoute()
    {
        $routeOptions = [
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@noParameters",
            "middleware" => ["foo3", "foo4"]
        ];
        $groupOptions = [
            "path" => "/foo",
            "middleware" => ["foo1", "foo2"]
        ];
        $this->router->group($groupOptions, function () use ($routeOptions)
        {
            $this->router->addRoute(new Route(Request::METHOD_GET, "/bar", $routeOptions));
            $this->router->delete("/blah", $routeOptions);
        });
        $this->router->get("/asdf", $routeOptions);
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(Request::METHOD_GET);
        /** @var Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRouteCollection()->get(Request::METHOD_DELETE);
        $this->assertEquals("/foo/bar", $getRoutes[0]->getRawPath());
        $this->assertEquals(["foo1", "foo2", "foo3", "foo4"], $getRoutes[0]->getMiddleware());
        $this->assertEquals("/asdf", $getRoutes[1]->getRawPath());
        $this->assertEquals(["foo3", "foo4"], $getRoutes[1]->getMiddleware());
        $this->assertEquals("/foo/blah", $deleteRoutes[0]->getRawPath());
        $this->assertEquals(["foo1", "foo2", "foo3", "foo4"], $deleteRoutes[0]->getMiddleware());
    }

    /**
     * Tests grouping routes that use a string for the middleware
     */
    public function testGroupingRoutesWithStringMiddleware()
    {
        $routeOptions = [
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@noParameters",
            "middleware" => "foo2"
        ];
        $groupOptions = [
            "path" => "/foo",
            "middleware" => "foo1"
        ];
        $this->router->group($groupOptions, function () use ($routeOptions)
        {
            $this->router->addRoute(new Route(Request::METHOD_GET, "/bar", $routeOptions));
            $this->router->delete("/blah", $routeOptions);
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(Request::METHOD_GET);
        /** @var Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRouteCollection()->get(Request::METHOD_DELETE);
        $this->assertEquals(["foo1", "foo2"], $getRoutes[0]->getMiddleware());
        $this->assertEquals(["foo1", "foo2"], $deleteRoutes[0]->getMiddleware());
    }

    /**
     * Tests specifying an invalid route controller name in the constructor
     */
    public function testInvalidMissedRouteControllerNameInConstructor()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $compiler = new Compiler(new Parser());
        new Router(new Dispatcher(new Container()), $compiler, "Class\\That\\Does\\Not\\Exist");
    }

    /**
     * Tests specifying an invalid route controller name in the setter
     */
    public function testInvalidMissedRouteControllerNameInSetter()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->router->setMissedRouteControllerName("Class\\That\\Does\\Not\\Exist");
    }

    /**
     * Tests that the matched controller is null before routing
     */
    public function testMatchedControllerIsNullBeforeRouting()
    {
        $this->assertNull($this->router->getMatchedController());
    }

    /**
     * Tests mixing HTTPS on nested groups
     */
    public function testMixingHTTPSOnNestedGroups()
    {
        $this->router->group(["https" => true], function ()
        {
            $this->router->group(["https" => false], function ()
            {
                $this->router->get("/foo", ["controller" => "foo@bar"]);
                $this->router->post("/foo", ["controller" => "foo@bar"]);
            });
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(Request::METHOD_GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(Request::METHOD_POST);
        $this->assertTrue($getRoutes[0]->isSecure());
        $this->assertTrue($postRoutes[0]->isSecure());
    }

    /**
     * Tests nested grouped routes
     */
    public function testNestedGroupedRoutes()
    {
        $outerGroupOptions = [
            "path" => "/foo",
            "controllerNamespace" => "RDev\\Tests\\HTTP\\Routing",
            "middleware" => ["foo1", "foo2"]
        ];
        $outerRouteOptions = ["controller" => "Mocks\\Controller@noParameters"];
        $innerRouteOptions = ["controller" => "Controller@noParameters"];
        $this->router->group($outerGroupOptions, function () use ($outerRouteOptions, $innerRouteOptions)
        {
            $this->router->addRoute(new Route(Request::METHOD_GET, "/bar", $outerRouteOptions));
            $this->router->delete("/blah", $outerRouteOptions);
            $innerGroupOptions = [
                "path" => "/asdf",
                "controllerNamespace" => "Mocks",
                "middleware" => ["foo3", "foo4"]
            ];
            $this->router->group($innerGroupOptions, function () use ($innerRouteOptions)
            {
                $this->router->get("/jkl", $innerRouteOptions);
            });
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(Request::METHOD_GET);
        /** @var Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRouteCollection()->get(Request::METHOD_DELETE);
        $this->assertEquals("/foo/bar", $getRoutes[0]->getRawPath());
        $this->assertEquals("RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller", $getRoutes[0]->getControllerName());
        $this->assertEquals(["foo1", "foo2"], $getRoutes[0]->getMiddleware());
        $this->assertEquals("/foo/asdf/jkl", $getRoutes[1]->getRawPath());
        $this->assertEquals("RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller", $getRoutes[1]->getControllerName());
        $this->assertEquals(["foo1", "foo2", "foo3", "foo4"], $getRoutes[1]->getMiddleware());
        $this->assertEquals("/foo/blah", $deleteRoutes[0]->getRawPath());
        $this->assertEquals("RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller", $deleteRoutes[0]->getControllerName());
        $this->assertEquals(["foo1", "foo2"], $deleteRoutes[0]->getMiddleware());
    }

    /**
     * Tests that nested groups with variable regexes overwrite one another
     */
    public function testNestingGroupVariableRegexesOverwriteOneAnother()
    {
        $this->router->group(["path" => "/users/{userId}", "variables" => ["id" => "\d*"]], function ()
        {
            $this->router->get("/foo", ["controller" => "foo@bar"]);
            // This route's variable regex should take precedence
            $this->router->get("/bam", ["controller" => "foo@bam", "variables" => ["id" => "\w+"]]);

            $this->router->group(["path" => "/bar", "variables" => ["id" => "\d+"]], function()
            {
                $this->router->get("/baz", ["controller" => "bar@baz"]);
            });
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(Request::METHOD_GET);
        $this->assertEquals("\d*", $getRoutes[0]->getVariableRegex("id"));
        $this->assertEquals("\w+", $getRoutes[1]->getVariableRegex("id"));
        $this->assertEquals("\d+", $getRoutes[2]->getVariableRegex("id"));
    }

    /**
     * Tests routing for any method
     */
    public function testRoutingAnyMethod()
    {
        $options = [
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@noParameters"
        ];
        $this->router->any("/foo", $options);
        $allRoutes = $this->router->getRouteCollection()->get();
        $this->assertEquals(1, count($allRoutes[Request::METHOD_GET]));
        $this->assertEquals(1, count($allRoutes[Request::METHOD_POST]));
        $this->assertEquals(1, count($allRoutes[Request::METHOD_DELETE]));
        $this->assertEquals(1, count($allRoutes[Request::METHOD_PUT]));
    }

    /**
     * Tests routing a DELETE request
     */
    public function testRoutingDeleteRequest()
    {
        $this->doTestForHTTPMethod(Request::METHOD_DELETE);
    }

    /**
     * Tests routing a GET request
     */
    public function testRoutingGetRequest()
    {
        $this->doTestForHTTPMethod(Request::METHOD_GET);
    }

    /**
     * Tests routing a HEAD request
     */
    public function testRoutingHeadRequest()
    {
        $this->doTestForHTTPMethod(Request::METHOD_HEAD);
    }

    /**
     * Tests routing a missing path
     */
    public function testRoutingMissingPath()
    {
        $request = new Request([], [], [], [
            "REQUEST_METHOD" => Request::METHOD_GET,
            "REQUEST_URI" => "/foo/"
        ], [], []);
        $response = $this->router->route($request);
        $this->assertInstanceOf("RDev\\HTTP\\Responses\\Response", $response);
        $this->assertInstanceOf("RDev\\HTTP\\Routing\\Controller", $this->router->getMatchedController());
        $this->assertNull($this->router->getMatchedRoute());
        $this->assertEquals(ResponseHeaders::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEmpty($response->getContent());
    }

    /**
     * Tests routing a missing path with a custom controller
     */
    public function testRoutingMissingPathWithCustomController()
    {
        $request = new Request([], [], [], [
            "REQUEST_METHOD" => Request::METHOD_GET,
            "REQUEST_URI" => "/foo/"
        ], [], []);
        $this->router->setMissedRouteControllerName("RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller");
        $response = $this->router->route($request);
        $this->assertInstanceOf("RDev\\HTTP\\Responses\\Response", $response);
        $this->assertEquals(ResponseHeaders::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals("foo", $response->getContent());
    }

    /**
     * Tests routing for multiple methods
     */
    public function testRoutingMultipleMethods()
    {
        $options = [
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@noParameters"
        ];
        $this->router->multiple([Request::METHOD_GET, Request::METHOD_POST], "/foo", $options);
        $allRoutes = $this->router->getRouteCollection()->get();
        $this->assertEquals(1, count($allRoutes[Request::METHOD_GET]));
        $this->assertEquals(1, count($allRoutes[Request::METHOD_POST]));
        $this->assertEquals(0, count($allRoutes[Request::METHOD_DELETE]));
        $this->assertEquals(0, count($allRoutes[Request::METHOD_PUT]));
        $this->assertEquals(0, count($allRoutes[Request::METHOD_HEAD]));
        $this->assertEquals(0, count($allRoutes[Request::METHOD_OPTIONS]));
        $this->assertEquals(0, count($allRoutes[Request::METHOD_PATCH]));
    }

    /**
     * Tests routing a OPTIONS request
     */
    public function testRoutingOptionsRequest()
    {
        $this->doTestForHTTPMethod(Request::METHOD_OPTIONS);
    }

    /**
     * Tests routing a PATCH request
     */
    public function testRoutingPatchRequest()
    {
        $this->doTestForHTTPMethod(Request::METHOD_PATCH);
    }

    /**
     * Tests routing a POST request
     */
    public function testRoutingPostRequest()
    {
        $this->doTestForHTTPMethod(Request::METHOD_POST);
    }

    /**
     * Tests routing a PUT request
     */
    public function testRoutingPutRequest()
    {
        $this->doTestForHTTPMethod(Request::METHOD_PUT);
    }

    /**
     * Tests a secure group
     */
    public function testSecureGroup()
    {
        $this->router->group(["https" => true], function ()
        {
            $this->router->get("/foo", ["controller" => "foo@bar"]);
            $this->router->post("/foo", ["controller" => "foo@bar"]);
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(Request::METHOD_GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(Request::METHOD_POST);
        $this->assertTrue($getRoutes[0]->isSecure());
        $this->assertTrue($postRoutes[0]->isSecure());
    }

    /**
     * Tests specifying a group host
     */
    public function testSpecifyingGroupHost()
    {
        $this->router->group(["host" => "google.com"], function ()
        {
            $this->router->get("/foo", ["controller" => "foo@bar"]);
            $this->router->post("/foo", ["controller" => "foo@bar"]);
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(Request::METHOD_GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(Request::METHOD_POST);
        $this->assertEquals("google.com", $getRoutes[0]->getRawHost());
        $this->assertEquals("google.com", $postRoutes[0]->getRawHost());
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
        $getRoutes = $this->router->getRouteCollection()->get(Request::METHOD_GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(Request::METHOD_POST);
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
        $getRoutes = $this->router->getRouteCollection()->get(Request::METHOD_GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(Request::METHOD_POST);
        $this->assertEquals("MyApp\\Controllers\\ControllerA", $getRoutes[0]->getControllerName());
        $this->assertEquals("MyApp\\Controllers\\ControllerB", $postRoutes[0]->getControllerName());
    }

    /**
     * Tests specifying a nested group hosts
     */
    public function testSpecifyingNestedGroupHosts()
    {
        $this->router->group(["host" => "google.com"], function ()
        {
            $this->router->group(["host" => "mail."], function ()
            {
                $this->router->get("/foo", ["controller" => "foo@bar"]);
                $this->router->post("/foo", ["controller" => "foo@bar"]);
            });
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(Request::METHOD_GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(Request::METHOD_POST);
        $this->assertEquals("mail.google.com", $getRoutes[0]->getRawHost());
        $this->assertEquals("mail.google.com", $postRoutes[0]->getRawHost());
    }

    /**
     * Sets up a router and does the routing and testing
     *
     * @param string $httpMethod The HTTP method to simulate in the call
     * @param string $rawPath The raw path the routes should use
     * @param string $pathToRoute The path to route
     * @param string $rawHost The raw host the routes should use
     * @param string $hostToRoute The host to route
     * @param string $controllerName The name of the controller to call
     * @param string $controllerMethod The name of the method in the mock controller to call
     */
    private function doRoute(
        $httpMethod,
        $rawPath,
        $pathToRoute,
        $rawHost,
        $hostToRoute,
        $controllerName,
        $controllerMethod
    )
    {
        $options = [
            "controller" => "$controllerName@$controllerMethod",
            "host" => $rawHost
        ];

        // The mock router will return the route used rather than the output of the route controller
        // This makes testing easier
        $mockRouter = new MockRouter();
        $deleteRoute = new Route(Request::METHOD_DELETE, $rawPath, $options);
        $getRoute = new Route(Request::METHOD_GET, $rawPath, $options);
        $postRoute = new Route(Request::METHOD_POST, $rawPath, $options);
        $putRoute = new Route(Request::METHOD_PUT, $rawPath, $options);
        $headRoute = new Route(Request::METHOD_HEAD, $rawPath, $options);
        $optionsRoute = new Route(Request::METHOD_OPTIONS, $rawPath, $options);
        $patchRoute = new Route(Request::METHOD_PATCH, $rawPath, $options);
        $mockRouter->addRoute($deleteRoute);
        $mockRouter->addRoute($getRoute);
        $mockRouter->addRoute($postRoute);
        $mockRouter->addRoute($putRoute);
        $mockRouter->addRoute($headRoute);
        $mockRouter->addRoute($optionsRoute);
        $mockRouter->addRoute($patchRoute);
        $server = [
            "REQUEST_METHOD" => $httpMethod,
            "REQUEST_URI" => $pathToRoute,
            "HTTP_HOST" => $hostToRoute
        ];
        $request = new Request([], [], [], $server, [], []);
        $routeToHandle = null;

        switch($httpMethod)
        {
            case Request::METHOD_DELETE:
                $routeToHandle = $deleteRoute;

                break;
            case Request::METHOD_GET:
                $routeToHandle = $getRoute;

                break;
            case Request::METHOD_POST:
                $routeToHandle = $postRoute;

                break;
            case Request::METHOD_PUT:
                $routeToHandle = $putRoute;

                break;
            case Request::METHOD_HEAD:
                $routeToHandle = $headRoute;

                break;
            case Request::METHOD_OPTIONS:
                $routeToHandle = $optionsRoute;

                break;
            case Request::METHOD_PATCH:
                $routeToHandle = $patchRoute;

                break;
        }

        $compiledRoute = $this->compiler->compile($routeToHandle, $request);
        $this->assertEquals($compiledRoute, $mockRouter->route($request));
        $this->assertEquals($compiledRoute, $mockRouter->getMatchedRoute());
        // The mock router does not actually instantiate the input controller
        // Instead, its dispatcher always sets the controller to the same object every time
        $this->assertInstanceOf("RDev\\HTTP\\Routing\\Controller", $mockRouter->getMatchedController());
    }

    /**
     * Tests a request with the input HTTP method
     *
     * @param string $httpMethod The HTTP method to test
     */
    private function doTestForHTTPMethod($httpMethod)
    {
        $this->doRoute(
            $httpMethod,
            "/foo",
            "/foo",
            "google.com",
            "google.com",
            "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller",
            "noParameters"
        );
        $this->doRoute(
            $httpMethod,
            "/foo/{foo}",
            "/foo/123",
            "{bar}.google.com",
            "mail.google.com",
            "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller",
            "twoParameters"
        );
        $this->doRoute(
            $httpMethod,
            "/foo/{foo}/{bar}",
            "/foo/123/456",
            "{baz}.{blah}.google.com",
            "u.mail.google.com",
            "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller",
            "severalParameters"
        );
    }
} 
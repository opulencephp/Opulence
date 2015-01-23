<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the router
 */
namespace RDev\HTTP\Routing;
use RDev\HTTP\Requests;
use RDev\HTTP\Responses;
use RDev\HTTP\Routing\Compilers\Parsers;
use RDev\IoC;
use RDev\Tests\Routing\Mocks;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    /** @var Mocks\Router The router to use in tests */
    private $router = null;
    /** @var Compilers\Compiler The compiler to use */
    private $compiler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $container = new IoC\Container();
        $this->compiler = new Compilers\Compiler(new Parsers\Parser());
        $this->router = new Router(new Dispatchers\Dispatcher($container), $this->compiler);
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

        foreach(Routes\Routes::getMethods() as $method)
        {
            call_user_func_array([$this->router, strtolower($method)], [$path, $options]);
            $expectedRoute = new Routes\Route($method, $path, $options);
            $this->assertEquals([$expectedRoute], $this->router->getRoutes()->get($method));
        }
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
            $routeOptions = ["controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@noParameters"];
            $this->router->addRoute(new Routes\Route(Requests\Request::METHOD_GET, "/bar", $routeOptions));
            $this->router->delete("/blah", $routeOptions);
        });
        /** @var Routes\Route[] $getRoutes */
        $getRoutes = $this->router->getRoutes()->get(Requests\Request::METHOD_GET);
        /** @var Routes\Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRoutes()->get(Requests\Request::METHOD_DELETE);
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
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@noParameters",
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
            $this->router->addRoute(new Routes\Route(Requests\Request::METHOD_GET, "/bar", $routeOptions));
            $this->router->delete("/blah", $routeOptions);
        });
        $this->router->get("/asdf", $routeOptions);
        /** @var Routes\Route[] $getRoutes */
        $getRoutes = $this->router->getRoutes()->get(Requests\Request::METHOD_GET);
        /** @var Routes\Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRoutes()->get(Requests\Request::METHOD_DELETE);
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
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@noParameters",
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
            $this->router->addRoute(new Routes\Route(Requests\Request::METHOD_GET, "/bar", $routeOptions));
            $this->router->delete("/blah", $routeOptions);
        });
        /** @var Routes\Route[] $getRoutes */
        $getRoutes = $this->router->getRoutes()->get(Requests\Request::METHOD_GET);
        /** @var Routes\Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRoutes()->get(Requests\Request::METHOD_DELETE);
        $this->assertEquals(["pre1", "pre2"], $getRoutes[0]->getPreFilters());
        $this->assertEquals(["post1", "post2"], $getRoutes[0]->getPostFilters());
        $this->assertEquals(["pre1", "pre2"], $deleteRoutes[0]->getPreFilters());
        $this->assertEquals(["post1", "post2"], $deleteRoutes[0]->getPostFilters());
    }

    /**
     * Tests specifying an invalid route controller name in the constructor
     */
    public function testInvalidMissedRouteControllerNameInConstructor()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $compiler = new Compilers\Compiler(new Parsers\Parser());
        new Router(new Dispatchers\Dispatcher(new IoC\Container()), $compiler, "Class\\That\\Does\\Not\\Exist");
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
        /** @var Routes\Route[] $getRoutes */
        $getRoutes = $this->router->getRoutes()->get(Requests\Request::METHOD_GET);
        /** @var Routes\Route[] $postRoutes */
        $postRoutes = $this->router->getRoutes()->get(Requests\Request::METHOD_POST);
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
            "pre" => ["pre1", "pre2"],
            "post" => ["post1", "post2"]
        ];
        $outerRouteOptions = ["controller" => "Mocks\\Controller@noParameters"];
        $innerRouteOptions = ["controller" => "Controller@noParameters"];
        $this->router->group($outerGroupOptions, function () use ($outerRouteOptions, $innerRouteOptions)
        {
            $this->router->addRoute(new Routes\Route(Requests\Request::METHOD_GET, "/bar", $outerRouteOptions));
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
        /** @var Routes\Route[] $getRoutes */
        $getRoutes = $this->router->getRoutes()->get(Requests\Request::METHOD_GET);
        /** @var Routes\Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRoutes()->get(Requests\Request::METHOD_DELETE);
        $this->assertEquals("/foo/bar", $getRoutes[0]->getRawPath());
        $this->assertEquals("RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller", $getRoutes[0]->getControllerName());
        $this->assertEquals(["pre1", "pre2"], $getRoutes[0]->getPreFilters());
        $this->assertEquals(["post1", "post2"], $getRoutes[0]->getPostFilters());
        $this->assertEquals("/foo/asdf/jkl", $getRoutes[1]->getRawPath());
        $this->assertEquals("RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller", $getRoutes[1]->getControllerName());
        $this->assertEquals(["pre1", "pre2", "pre3", "pre4"], $getRoutes[1]->getPreFilters());
        $this->assertEquals(["post1", "post2", "post3", "post4"], $getRoutes[1]->getPostFilters());
        $this->assertEquals("/foo/blah", $deleteRoutes[0]->getRawPath());
        $this->assertEquals("RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller", $deleteRoutes[0]->getControllerName());
        $this->assertEquals(["pre1", "pre2"], $deleteRoutes[0]->getPreFilters());
        $this->assertEquals(["post1", "post2"], $deleteRoutes[0]->getPostFilters());
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
        $allRoutes = $this->router->getRoutes()->get();
        $this->assertEquals(1, count($allRoutes[Requests\Request::METHOD_GET]));
        $this->assertEquals(1, count($allRoutes[Requests\Request::METHOD_POST]));
        $this->assertEquals(1, count($allRoutes[Requests\Request::METHOD_DELETE]));
        $this->assertEquals(1, count($allRoutes[Requests\Request::METHOD_PUT]));
    }

    /**
     * Tests routing a DELETE request
     */
    public function testRoutingDeleteRequest()
    {
        $this->doTestForHTTPMethod(Requests\Request::METHOD_DELETE);
    }

    /**
     * Tests routing a GET request
     */
    public function testRoutingGetRequest()
    {
        $this->doTestForHTTPMethod(Requests\Request::METHOD_GET);
    }

    /**
     * Tests routing a HEAD request
     */
    public function testRoutingHeadRequest()
    {
        $this->doTestForHTTPMethod(Requests\Request::METHOD_HEAD);
    }

    /**
     * Tests routing a missing path
     */
    public function testRoutingMissingPath()
    {
        $request = new Requests\Request([], [], [], [
            "REQUEST_METHOD" => Requests\Request::METHOD_GET,
            "REQUEST_URI" => "/foo/"
        ], [], []);
        $response = $this->router->route($request);
        $this->assertInstanceOf("RDev\\HTTP\\Responses\\Response", $response);
        $this->assertEquals(Responses\ResponseHeaders::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEmpty($response->getContent());
    }

    /**
     * Tests routing a missing path with a custom controller
     */
    public function testRoutingMissingPathWithCustomController()
    {
        $request = new Requests\Request([], [], [], [
            "REQUEST_METHOD" => Requests\Request::METHOD_GET,
            "REQUEST_URI" => "/foo/"
        ], [], []);
        $this->router->setMissedRouteControllerName("RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller");
        $response = $this->router->route($request);
        $this->assertInstanceOf("RDev\\HTTP\\Responses\\Response", $response);
        $this->assertEquals(Responses\ResponseHeaders::HTTP_NOT_FOUND, $response->getStatusCode());
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
        $this->router->multiple([Requests\Request::METHOD_GET, Requests\Request::METHOD_POST], "/foo", $options);
        $allRoutes = $this->router->getRoutes()->get();
        $this->assertEquals(1, count($allRoutes[Requests\Request::METHOD_GET]));
        $this->assertEquals(1, count($allRoutes[Requests\Request::METHOD_POST]));
        $this->assertEquals(0, count($allRoutes[Requests\Request::METHOD_DELETE]));
        $this->assertEquals(0, count($allRoutes[Requests\Request::METHOD_PUT]));
        $this->assertEquals(0, count($allRoutes[Requests\Request::METHOD_HEAD]));
        $this->assertEquals(0, count($allRoutes[Requests\Request::METHOD_OPTIONS]));
        $this->assertEquals(0, count($allRoutes[Requests\Request::METHOD_PATCH]));
    }

    /**
     * Tests routing a OPTIONS request
     */
    public function testRoutingOptionsRequest()
    {
        $this->doTestForHTTPMethod(Requests\Request::METHOD_OPTIONS);
    }

    /**
     * Tests routing a PATCH request
     */
    public function testRoutingPatchRequest()
    {
        $this->doTestForHTTPMethod(Requests\Request::METHOD_PATCH);
    }

    /**
     * Tests routing a POST request
     */
    public function testRoutingPostRequest()
    {
        $this->doTestForHTTPMethod(Requests\Request::METHOD_POST);
    }

    /**
     * Tests routing a PUT request
     */
    public function testRoutingPutRequest()
    {
        $this->doTestForHTTPMethod(Requests\Request::METHOD_PUT);
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
        /** @var Routes\Route[] $getRoutes */
        $getRoutes = $this->router->getRoutes()->get(Requests\Request::METHOD_GET);
        /** @var Routes\Route[] $postRoutes */
        $postRoutes = $this->router->getRoutes()->get(Requests\Request::METHOD_POST);
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
        /** @var Routes\Route[] $getRoutes */
        $getRoutes = $this->router->getRoutes()->get(Requests\Request::METHOD_GET);
        /** @var Routes\Route[] $postRoutes */
        $postRoutes = $this->router->getRoutes()->get(Requests\Request::METHOD_POST);
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
        /** @var Routes\Route[] $getRoutes */
        $getRoutes = $this->router->getRoutes()->get(Requests\Request::METHOD_GET);
        /** @var Routes\Route[] $postRoutes */
        $postRoutes = $this->router->getRoutes()->get(Requests\Request::METHOD_POST);
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
        /** @var Routes\Route[] $getRoutes */
        $getRoutes = $this->router->getRoutes()->get(Requests\Request::METHOD_GET);
        /** @var Routes\Route[] $postRoutes */
        $postRoutes = $this->router->getRoutes()->get(Requests\Request::METHOD_POST);
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
        /** @var Routes\Route[] $getRoutes */
        $getRoutes = $this->router->getRoutes()->get(Requests\Request::METHOD_GET);
        /** @var Routes\Route[] $postRoutes */
        $postRoutes = $this->router->getRoutes()->get(Requests\Request::METHOD_POST);
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
        $mockRouter = new Mocks\Router();
        $deleteRoute = new Routes\Route(Requests\Request::METHOD_DELETE, $rawPath, $options);
        $getRoute = new Routes\Route(Requests\Request::METHOD_GET, $rawPath, $options);
        $postRoute = new Routes\Route(Requests\Request::METHOD_POST, $rawPath, $options);
        $putRoute = new Routes\Route(Requests\Request::METHOD_PUT, $rawPath, $options);
        $headRoute = new Routes\Route(Requests\Request::METHOD_HEAD, $rawPath, $options);
        $optionsRoute = new Routes\Route(Requests\Request::METHOD_OPTIONS, $rawPath, $options);
        $patchRoute = new Routes\Route(Requests\Request::METHOD_PATCH, $rawPath, $options);
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
        $request = new Requests\Request([], [], [], $server, [], []);
        $routeToHandle = null;

        switch($httpMethod)
        {
            case Requests\Request::METHOD_DELETE:
                $routeToHandle = $deleteRoute;

                break;
            case Requests\Request::METHOD_GET:
                $routeToHandle = $getRoute;

                break;
            case Requests\Request::METHOD_POST:
                $routeToHandle = $postRoute;

                break;
            case Requests\Request::METHOD_PUT:
                $routeToHandle = $putRoute;

                break;
            case Requests\Request::METHOD_HEAD:
                $routeToHandle = $headRoute;

                break;
            case Requests\Request::METHOD_OPTIONS:
                $routeToHandle = $optionsRoute;

                break;
            case Requests\Request::METHOD_PATCH:
                $routeToHandle = $patchRoute;

                break;
        }

        $compiledRoute = $this->compiler->compile($routeToHandle, $request);
        $this->assertEquals($compiledRoute, $mockRouter->route($request));
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
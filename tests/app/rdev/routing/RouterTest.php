<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the router
 */
namespace RDev\Routing;
use RDev\HTTP;
use RDev\IoC;
use RDev\Tests\Routing\Mocks;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    /** @var Mocks\Router The router to use in tests */
    private $router = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $container = new IoC\Container();
        $this->router = new Router(new Dispatcher($container), new Compilers\Compiler());
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
        $methods = [
            HTTP\Request::METHOD_GET,
            HTTP\Request::METHOD_POST,
            HTTP\Request::METHOD_DELETE,
            HTTP\Request::METHOD_PUT,
            HTTP\Request::METHOD_HEAD,
            HTTP\Request::METHOD_OPTIONS,
            HTTP\Request::METHOD_PATCH
        ];

        foreach($methods as $method)
        {
            call_user_func_array([$this->router, strtolower($method)], [$path, $options]);
            $expectedRoute = new Route($method, $path, $options);
            $this->assertEquals([$expectedRoute], $this->router->getRoutes($method));
        }
    }

    /**
     * Tests getting an invalid method's routes
     */
    public function testGettingInvalidMethodRoutes()
    {
        $path = "/foo";
        $options = [
            "controller" => "RDev\\Tests\\Routing\\Mocks\\Controller@noParameters"
        ];
        $this->router->delete($path, $options);
        $this->router->get($path, $options);
        $this->router->post($path, $options);
        $this->router->put($path, $options);
        $this->router->head($path, $options);
        $this->router->options($path, $options);
        $this->router->patch($path, $options);
        $this->assertEquals([], $this->router->getRoutes("methodThatDoeNotExist"));
    }

    /**
     * Tests getting a named route
     */
    public function testGettingNamedRoute()
    {
        $path = "/foo";
        $options = [
            "controller" => "RDev\\Tests\\Routing\\Mocks\\Controller@noParameters",
            "name" => "blah"
        ];
        $expectedRoute = new Route(HTTP\Request::METHOD_GET, $path, $options);
        $this->router->addRoute($expectedRoute);
        $this->assertEquals($expectedRoute, $this->router->getNamedRoute("blah"));
        // First, reset the router
        // Do the test by calling the verb
        $this->router = new Router(new Dispatcher(new IoC\Container()), new Compilers\Compiler());
        $this->router->get($path, $options);
        $this->assertEquals($expectedRoute, $this->router->getNamedRoute("blah"));
    }

    /**
     * Tests getting a non-existent named route
     */
    public function testGettingNonExistentNamedRoute()
    {
        $path = "/foo";
        $options = [
            "controller" => "RDev\\Tests\\Routing\\Mocks\\Controller@noParameters"
        ];
        $this->router->get($path, $options);
        $this->assertNull($this->router->getNamedRoute("blah"));
    }

    /**
     * Tests getting the routes
     */
    public function testGettingRoutes()
    {
        $path = "/foo";
        $options = [
            "controller" => "RDev\\Tests\\Routing\\Mocks\\Controller@noParameters"
        ];
        $deleteRoute = new Route(HTTP\Request::METHOD_DELETE, $path, $options);
        $getRoute = new Route(HTTP\Request::METHOD_GET, $path, $options);
        $postRoute = new Route(HTTP\Request::METHOD_POST, $path, $options);
        $putRoute = new Route(HTTP\Request::METHOD_PUT, $path, $options);
        $headRoute = new Route(HTTP\Request::METHOD_HEAD, $path, $options);
        $optionsRoute = new Route(HTTP\Request::METHOD_OPTIONS, $path, $options);
        $patchRoute = new Route(HTTP\Request::METHOD_PATCH, $path, $options);
        $this->router->addRoute($deleteRoute);
        $this->router->addRoute($getRoute);
        $this->router->addRoute($postRoute);
        $this->router->addRoute($putRoute);
        $this->router->addRoute($headRoute);
        $this->router->addRoute($optionsRoute);
        $this->router->addRoute($patchRoute);
        $allRoutes = $this->router->getRoutes();
        $this->assertSame([$deleteRoute], $allRoutes[HTTP\Request::METHOD_DELETE]);
        $this->assertSame([$getRoute], $allRoutes[HTTP\Request::METHOD_GET]);
        $this->assertSame([$postRoute], $allRoutes[HTTP\Request::METHOD_POST]);
        $this->assertSame([$putRoute], $allRoutes[HTTP\Request::METHOD_PUT]);
        $this->assertSame([$headRoute], $allRoutes[HTTP\Request::METHOD_HEAD]);
        $this->assertSame([$optionsRoute], $allRoutes[HTTP\Request::METHOD_OPTIONS]);
        $this->assertSame([$patchRoute], $allRoutes[HTTP\Request::METHOD_PATCH]);
    }

    /**
     * Tests getting a specific method's routes
     */
    public function testGettingSpecificMethodRoutes()
    {
        $path = "/foo";
        $options = [
            "controller" => "RDev\\Tests\\Routing\\Mocks\\Controller@noParameters"
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
            $routeOptions = ["controller" => "RDev\\Tests\\Routing\\Mocks\\Controller@noParameters"];
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
            "controller" => "RDev\\Tests\\Routing\\Mocks\\Controller@noParameters",
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
            "controller" => "RDev\\Tests\\Routing\\Mocks\\Controller@noParameters",
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
     * Tests matching an insecure route over HTTPS
     */
    public function testMatchingInsecureRouteOnHTTPS()
    {
        $options = [
            "controller" => "RDev\\Tests\\Routing\\Mocks\\Controller@noParameters"
        ];
        $this->router->get("/", $options);
        $request = new HTTP\Request([], [], [], [
            "REQUEST_METHOD" => HTTP\Request::METHOD_GET,
            "REQUEST_URI" => "/",
            "HTTPS" => true
        ], [], []);
        $this->assertEquals("noParameters", $this->router->route($request)->getContent());
    }

    /**
     * Tests matching a secure route
     */
    public function testMatchingSecureRoute()
    {
        $options = [
            "controller" => "RDev\\Tests\\Routing\\Mocks\\Controller@noParameters",
            "https" => true
        ];
        $this->router->get("/", $options);
        $request = new HTTP\Request([], [], [], [
            "REQUEST_METHOD" => HTTP\Request::METHOD_GET,
            "REQUEST_URI" => "/",
            "HTTPS" => true
        ], [], []);
        $this->assertEquals("noParameters", $this->router->route($request)->getContent());
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
        $getRoutes = $this->router->getRoutes(HTTP\Request::METHOD_GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRoutes(HTTP\Request::METHOD_POST);
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
            "controllerNamespace" => "RDev\\Tests\\Routing",
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
        $this->assertEquals("RDev\\Tests\\Routing\\Mocks\\Controller", $getRoutes[0]->getControllerName());
        $this->assertEquals(["pre1", "pre2"], $getRoutes[0]->getPreFilters());
        $this->assertEquals(["post1", "post2"], $getRoutes[0]->getPostFilters());
        $this->assertEquals("/foo/asdf/jkl", $getRoutes[1]->getRawPath());
        $this->assertEquals("RDev\\Tests\\Routing\\Mocks\\Controller", $getRoutes[1]->getControllerName());
        $this->assertEquals(["pre1", "pre2", "pre3", "pre4"], $getRoutes[1]->getPreFilters());
        $this->assertEquals(["post1", "post2", "post3", "post4"], $getRoutes[1]->getPostFilters());
        $this->assertEquals("/foo/blah", $deleteRoutes[0]->getRawPath());
        $this->assertEquals("RDev\\Tests\\Routing\\Mocks\\Controller", $deleteRoutes[0]->getControllerName());
        $this->assertEquals(["pre1", "pre2"], $deleteRoutes[0]->getPreFilters());
        $this->assertEquals(["post1", "post2"], $deleteRoutes[0]->getPostFilters());
    }

    /**
     * Tests trying to match a secure route when not running on HTTPS
     */
    public function testNotBeingHTTPSAndMatchingSecureRoute()
    {
        $options = [
            "controller" => "foo@bar",
            "https" => true
        ];
        $this->router->get("/", $options);
        $request = new HTTP\Request([], [], [], [
            "REQUEST_METHOD" => HTTP\Request::METHOD_GET,
            "REQUEST_URI" => "/"
        ], [], []);
        $this->router->setMissedRouteControllerName("RDev\\Tests\\Routing\\Mocks\\Controller");
        $response = $this->router->route($request);
        $this->assertInstanceOf("RDev\\HTTP\\Response", $response);
        $this->assertEquals(HTTP\ResponseHeaders::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals("foo", $response->getContent());
    }

    /**
     * Tests routing for any method
     */
    public function testRoutingAnyMethod()
    {
        $options = [
            "controller" => "RDev\\Tests\\Routing\\Mocks\\Controller@noParameters"
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
        $this->doTestForHTTPMethod(HTTP\Request::METHOD_DELETE);
    }

    /**
     * Tests routing a GET request
     */
    public function testRoutingGetRequest()
    {
        $this->doTestForHTTPMethod(HTTP\Request::METHOD_GET);
    }

    /**
     * Tests routing a HEAD request
     */
    public function testRoutingHeadRequest()
    {
        $this->doTestForHTTPMethod(HTTP\Request::METHOD_HEAD);
    }

    /**
     * Tests routing a missing path
     */
    public function testRoutingMissingPath()
    {
        $request = new HTTP\Request([], [], [], [
            "REQUEST_METHOD" => HTTP\Request::METHOD_GET,
            "REQUEST_URI" => "/foo/"
        ], [], []);
        $response = $this->router->route($request);
        $this->assertInstanceOf("RDev\\HTTP\\Response", $response);
        $this->assertEquals(HTTP\ResponseHeaders::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEmpty($response->getContent());
    }

    /**
     * Tests routing a missing path with a custom controller
     */
    public function testRoutingMissingPathWithCustomController()
    {
        $request = new HTTP\Request([], [], [], [
            "REQUEST_METHOD" => HTTP\Request::METHOD_GET,
            "REQUEST_URI" => "/foo/"
        ], [], []);
        $this->router->setMissedRouteControllerName("RDev\\Tests\\Routing\\Mocks\\Controller");
        $response = $this->router->route($request);
        $this->assertInstanceOf("RDev\\HTTP\\Response", $response);
        $this->assertEquals(HTTP\ResponseHeaders::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals("foo", $response->getContent());
    }

    /**
     * Tests routing for multiple methods
     */
    public function testRoutingMultipleMethods()
    {
        $options = [
            "controller" => "RDev\\Tests\\Routing\\Mocks\\Controller@noParameters"
        ];
        $this->router->multiple([HTTP\Request::METHOD_GET, HTTP\Request::METHOD_POST], "/foo", $options);
        $allRoutes = $this->router->getRoutes();
        $this->assertEquals(1, count($allRoutes[HTTP\Request::METHOD_GET]));
        $this->assertEquals(1, count($allRoutes[HTTP\Request::METHOD_POST]));
        $this->assertEquals(0, count($allRoutes[HTTP\Request::METHOD_DELETE]));
        $this->assertEquals(0, count($allRoutes[HTTP\Request::METHOD_PUT]));
        $this->assertEquals(0, count($allRoutes[HTTP\Request::METHOD_HEAD]));
        $this->assertEquals(0, count($allRoutes[HTTP\Request::METHOD_OPTIONS]));
        $this->assertEquals(0, count($allRoutes[HTTP\Request::METHOD_PATCH]));
    }

    /**
     * Tests routing a OPTIONS request
     */
    public function testRoutingOptionsRequest()
    {
        $this->doTestForHTTPMethod(HTTP\Request::METHOD_OPTIONS);
    }

    /**
     * Tests routing a PATCH request
     */
    public function testRoutingPatchRequest()
    {
        $this->doTestForHTTPMethod(HTTP\Request::METHOD_PATCH);
    }

    /**
     * Tests routing a POST request
     */
    public function testRoutingPostRequest()
    {
        $this->doTestForHTTPMethod(HTTP\Request::METHOD_POST);
    }

    /**
     * Tests routing a PUT request
     */
    public function testRoutingPutRequest()
    {
        $this->doTestForHTTPMethod(HTTP\Request::METHOD_PUT);
    }

    /**
     * Tests routing a route with an optional variable
     */
    public function testRoutingRouteWithOptionalVariable()
    {
        $options = [
            "controller" => "RDev\\Tests\\Routing\\Mocks\\Controller@noParameters"
        ];
        $this->router->get("/foo/{bar?}", $options);
        $request = new HTTP\Request([], [], [], [
            "REQUEST_METHOD" => HTTP\Request::METHOD_GET,
            "REQUEST_URI" => "/foo/"
        ], [], []);
        $this->assertEquals("noParameters", $this->router->route($request)->getContent());
    }

    /**
     * Tests routing a route with an optional variable with a default value
     */
    public function testRoutingRouteWithOptionalVariableWithDefaultValue()
    {
        $options = [
            "controller" => "RDev\\Tests\\Routing\\Mocks\\Controller@oneParameter"
        ];
        $this->router->get("/bar/{foo?=23}", $options);
        $request = new HTTP\Request([], [], [], [
            "REQUEST_METHOD" => HTTP\Request::METHOD_GET,
            "REQUEST_URI" => "/bar/"
        ], [], []);
        $this->assertEquals("foo:23", $this->router->route($request)->getContent());
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
        $getRoutes = $this->router->getRoutes(HTTP\Request::METHOD_GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRoutes(HTTP\Request::METHOD_POST);
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
        $getRoutes = $this->router->getRoutes(HTTP\Request::METHOD_GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRoutes(HTTP\Request::METHOD_POST);
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
        $getRoutes = $this->router->getRoutes(HTTP\Request::METHOD_GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRoutes(HTTP\Request::METHOD_POST);
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
        $getRoutes = $this->router->getRoutes(HTTP\Request::METHOD_GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRoutes(HTTP\Request::METHOD_POST);
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
        $getRoutes = $this->router->getRoutes(HTTP\Request::METHOD_GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRoutes(HTTP\Request::METHOD_POST);
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
        $deleteRoute = new Route(HTTP\Request::METHOD_DELETE, $rawPath, $options);
        $getRoute = new Route(HTTP\Request::METHOD_GET, $rawPath, $options);
        $postRoute = new Route(HTTP\Request::METHOD_POST, $rawPath, $options);
        $putRoute = new Route(HTTP\Request::METHOD_PUT, $rawPath, $options);
        $headRoute = new Route(HTTP\Request::METHOD_HEAD, $rawPath, $options);
        $optionsRoute = new Route(HTTP\Request::METHOD_OPTIONS, $rawPath, $options);
        $patchRoute = new Route(HTTP\Request::METHOD_PATCH, $rawPath, $options);
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
        $request = new HTTP\Request([], [], [], $server, [], []);

        switch($httpMethod)
        {
            case HTTP\Request::METHOD_DELETE:
                $this->assertSame($deleteRoute, $mockRouter->route($request));
                break;
            case HTTP\Request::METHOD_GET:
                $this->assertSame($getRoute, $mockRouter->route($request));
                break;
            case HTTP\Request::METHOD_POST:
                $this->assertSame($postRoute, $mockRouter->route($request));
                break;
            case HTTP\Request::METHOD_PUT:
                $this->assertSame($putRoute, $mockRouter->route($request));
                break;
            case HTTP\Request::METHOD_HEAD:
                $this->assertSame($headRoute, $mockRouter->route($request));
                break;
            case HTTP\Request::METHOD_OPTIONS:
                $this->assertSame($optionsRoute, $mockRouter->route($request));
                break;
            case HTTP\Request::METHOD_PATCH:
                $this->assertSame($patchRoute, $mockRouter->route($request));
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
        $this->doRoute(
            $httpMethod,
            "/foo",
            "/foo",
            "google.com",
            "google.com",
            "RDev\\Tests\\Routing\\Mocks\\Controller",
            "noParameters"
        );
        $this->doRoute(
            $httpMethod,
            "/foo/{foo}",
            "/foo/123",
            "{bar}.google.com",
            "mail.google.com",
            "RDev\\Tests\\Routing\\Mocks\\Controller",
            "twoParameters"
        );
        $this->doRoute(
            $httpMethod,
            "/foo/{foo}/{bar}",
            "/foo/123/456",
            "{baz}.{blah}.google.com",
            "u.mail.google.com",
            "RDev\\Tests\\Routing\\Mocks\\Controller",
            "severalParameters"
        );
    }
} 
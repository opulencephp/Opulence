<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the router
 */
namespace RDev\Models\Routing;
use RDev\Models\HTTP;
use RDev\Models\IoC;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    /** @var Router The router to use in tests */
    private $router = null;

    /**
     * Tests calling a non-existent controller
     */
    public function testCallingNonExistentController()
    {
        $this->setExpectedException("RDev\\Models\\Routing\\Exceptions\\RouteException");
        $this->setupRequest("GET", "/foo", "RDev\\Class\\That\\Does\\Not\\Exist", "foo");
        $this->router->route("/foo");
    }

    /**
     * Tests calling a method that does not exists in a controller
     */
    public function testCallingNonExistentMethod()
    {
        $this->setExpectedException("RDev\\Models\\Routing\\Exceptions\\RouteException");
        $this->setupRequest("GET", "/foo", "RDev\\Tests\\Controllers\\Mocks\\Controller", "doesNotExist");
        $this->router->route("/foo");
    }

    /**
     * Tests calling a private method in a controller
     */
    public function testCallingPrivateMethod()
    {
        $this->setExpectedException("RDev\\Models\\Routing\\Exceptions\\RouteException");
        $this->setupRequest("GET", "/foo", "RDev\\Tests\\Controllers\\Mocks\\Controller", "notPublic");
        $this->router->route("/foo");
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
        $deleteRoute = new Route([HTTP\Request::METHOD_DELETE], $path, $options);
        $getRoute = new Route([HTTP\Request::METHOD_GET], $path, $options);
        $postRoute = new Route([HTTP\Request::METHOD_POST], $path, $options);
        $putRoute = new Route([HTTP\Request::METHOD_PUT], $path, $options);
        $configArray = [
            "routes" => [
                $deleteRoute,
                $getRoute,
                $postRoute,
                $putRoute
            ]
        ];
        $this->router = new Router(new IoC\Container(), new HTTP\Connection, $configArray);
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
        $deleteRoute = new Route([HTTP\Request::METHOD_DELETE], $path, $options);
        $getRoute = new Route([HTTP\Request::METHOD_GET], $path, $options);
        $postRoute = new Route([HTTP\Request::METHOD_POST], $path, $options);
        $putRoute = new Route([HTTP\Request::METHOD_PUT], $path, $options);
        $configArray = [
            "routes" => [
                $deleteRoute,
                $getRoute,
                $postRoute,
                $putRoute
            ]
        ];
        $this->router = new Router(new IoC\Container(), new HTTP\Connection, $configArray);
        $this->assertEquals([
            HTTP\Request::METHOD_DELETE => [$deleteRoute],
            HTTP\Request::METHOD_GET => [$getRoute],
            HTTP\Request::METHOD_POST => [$postRoute],
            HTTP\Request::METHOD_PUT => [$putRoute]
        ], $this->router->getRoutes());
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
        $deleteRoute = new Route([HTTP\Request::METHOD_DELETE], $path, $options);
        $getRoute = new Route([HTTP\Request::METHOD_GET], $path, $options);
        $postRoute = new Route([HTTP\Request::METHOD_POST], $path, $options);
        $putRoute = new Route([HTTP\Request::METHOD_PUT], $path, $options);
        $configArray = [
            "routes" => [
                $deleteRoute,
                $getRoute,
                $postRoute,
                $putRoute
            ]
        ];
        $this->router = new Router(new IoC\Container(), new HTTP\Connection, $configArray);
        $this->assertEquals([$getRoute], $this->router->getRoutes(HTTP\Request::METHOD_GET));
    }

    /**
     * Tests routing for any method
     */
    public function testRoutingAnyMethod()
    {
        $this->router = new Router(new IoC\Container(), new HTTP\Connection(), []);
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
        $this->router = new Router(new IoC\Container(), new HTTP\Connection(), []);
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
     * Tests using a post-filter that does not return anything
     */
    public function testUsingPostFilterThatDoesNotReturnAnything()
    {
        $this->setupRequest("GET", "/foo", "RDev\\Tests\\Controllers\\Mocks\\Controller", "returnsNothing", [],
            ["foo"]);
        $this->router->registerFilter("foo", function ()
        {
            $foo = "bar";
        });
        $this->assertEquals("NOTHING", $this->router->route("/foo"));
    }

    /**
     * Tests using a post-filter that returns something
     */
    public function testUsingPostFilterThatReturnsSomething()
    {
        $this->setupRequest("GET", "/foo", "RDev\\Tests\\Controllers\\Mocks\\Controller", "returnsNothing", [],
            ["foo"]);
        $this->router->registerFilter("foo", function ()
        {
            return "YAY";
        });
        $this->assertEquals("YAY", $this->router->route("/foo"));
    }

    /**
     * Tests using a pre-filter that does not return anything
     */
    public function testUsingPreFilterThatDoesNotReturnAnything()
    {
        $this->setupRequest("GET", "/foo", "RDev\\Tests\\Controllers\\Mocks\\Controller", "noParameters", ["foo"],
            []);
        $this->router->registerFilter("foo", function ()
        {
            $foo = "bar";
        });
        $this->assertEquals("noParameters", $this->router->route("/foo"));
    }

    /**
     * Tests using a pre-filter that returns something
     */
    public function testUsingPreFilterThatReturnsSomething()
    {
        $this->setupRequest("GET", "/foo", "RDev\\Tests\\Controllers\\Mocks\\Controller", "returnsNothing", ["foo"],
            []);
        $this->router->registerFilter("foo", function ()
        {
            return "YAY";
        });
        $this->assertEquals("YAY", $this->router->route("/foo"));
    }

    /**
     * Tests using an unregistered post-filter
     */
    public function testUsingUnregisteredPostFilter()
    {
        $this->setExpectedException("RDev\\Models\\Routing\\Exceptions\\RouteException");
        $this->setupRequest("GET", "/foo", "RDev\\Tests\\Controllers\\Mocks\\Controller", "returnsNothing", [],
            ["fakeFilter"]);
        $this->router->route("/foo");
    }

    /**
     * Tests using an unregistered pre-filter
     */
    public function testUsingUnregisteredPreFilter()
    {
        $this->setExpectedException("RDev\\Models\\Routing\\Exceptions\\RouteException");
        $this->setupRequest("GET", "/foo", "RDev\\Tests\\Controllers\\Mocks\\Controller", "returnsNothing",
            ["fakeFilter"]);
        $this->router->route("/foo");
    }

    /**
     * Tests a request with the input HTTP method
     *
     * @param string $httpMethod The HTTP method to test
     */
    private function doTestForHTTPMethod($httpMethod)
    {
        $this->setupRequest($httpMethod, "/foo", "RDev\\Tests\\Controllers\\Mocks\\Controller", "noParameters");
        $this->assertEquals("noParameters", $this->router->route("/foo"));
        $this->setupRequest($httpMethod, "/foo/{foo}", "RDev\\Tests\\Controllers\\Mocks\\Controller", "oneParameter");
        $this->assertEquals("foo:123", $this->router->route("/foo/123"));
        $this->setupRequest($httpMethod, "/foo/{foo}/{bar}", "RDev\\Tests\\Controllers\\Mocks\\Controller",
            "multipleParameters");
        $this->assertEquals("foo:123, bar:456", $this->router->route("/foo/123/456"));
        $this->setupRequest($httpMethod, "/foo/{foo}/{bar}", "RDev\\Tests\\Controllers\\Mocks\\Controller",
            "multipleParametersWithDefaultValues");
        $this->assertEquals("foo:123, bar:456, blah:724", $this->router->route("/foo/123/456"));
    }

    /**
     * Sets up a request so we can test the router
     *
     * @param string $httpMethod The HTTP method to simulate in the call
     * @param string $path The raw path the routes should use
     * @param string $controllerName The name of the controller to call
     * @param string $controllerMethod The name of the method in the mock controller to call
     * @param array $preFilters The list of pre-filters to run
     * @param array $postFilters The list of post-filters to run
     */
    private function setupRequest($httpMethod, $path, $controllerName, $controllerMethod, array $preFilters = [],
                                  array $postFilters = [])
    {
        $options = [
            "controller" => "$controllerName@$controllerMethod"
        ];

        if(count($preFilters) > 0)
        {
            $options["pre"] = $preFilters;
        }

        if(count($postFilters) > 0)
        {
            $options["post"] = $postFilters;
        }

        $_SERVER["REQUEST_METHOD"] = $httpMethod;
        $configArray = [
            "routes" => [
                new Route([HTTP\Request::METHOD_DELETE], $path, $options),
                new Route([HTTP\Request::METHOD_GET], $path, $options),
                new Route([HTTP\Request::METHOD_POST], $path, $options),
                new Route([HTTP\Request::METHOD_PUT], $path, $options)
            ]
        ];
        $this->router = new Router(new IoC\Container(), new HTTP\Connection, $configArray);
        $allRoutes = $this->router->getRoutes();

        /** @var Route $route */
        foreach($configArray["routes"] as $route)
        {
            // This is testing that this route was added to the appropriate HTTP method
            $this->assertSame($route, $allRoutes[$route->getMethods()[0]][0]);
        }
    }
} 
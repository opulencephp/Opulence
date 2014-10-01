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
     * Tests calling a non-existent controller
     */
    public function testCallingNonExistentController()
    {
        $this->setExpectedException("RDev\\Models\\Web\\Routing\\Exceptions\\InvalidControllerException");
        $this->setupRequest("GET", "/foo", "RDev\\Class\\That\\Does\\Not\\Exist", "foo");
        $this->router->route("/foo");
    }

    /**
     * Tests calling a method that does not exists in a controller
     */
    public function testCallingNonExistentMethod()
    {
        $this->setExpectedException("RDev\\Models\\Web\\Routing\\Exceptions\\InvalidControllerException");
        $this->setupRequest("GET", "/foo", "RDev\\Tests\\Controllers\\Mocks\\Controller", "doesNotExist");
        $this->router->route("/foo");
    }

    /**
     * Tests calling a private method in a controller
     */
    public function testCallingPrivateMethod()
    {
        $this->setExpectedException("RDev\\Models\\Web\\Routing\\Exceptions\\InvalidControllerException");
        $this->setupRequest("GET", "/foo", "RDev\\Tests\\Controllers\\Mocks\\Controller", "notPublic");
        $this->router->route("/foo");
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
     * Tests using a after-filter that does not return anything
     */
    public function testUsingAfterFilterThatDoesNotReturnAnything()
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
     * Tests using an after-filter that returns something
     */
    public function testUsingAfterFilterThatReturnsSomething()
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
     * Tests using a before-filter that does not return anything
     */
    public function testUsingBeforeFilterThatDoesNotReturnAnything()
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
     * Tests using a before-filter that returns something
     */
    public function testUsingBeforeFilterThatReturnsSomething()
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
     * Tests using an unregistered after-filter
     */
    public function testUsingUnregisteredAfterFilter()
    {
        $this->setExpectedException("RDev\\Models\\Web\\Routing\\Exceptions\\InvalidFilterException");
        $this->setupRequest("GET", "/foo", "RDev\\Tests\\Controllers\\Mocks\\Controller", "returnsNothing", [],
            ["fakeFilter"]);
        $this->router->route("/foo");
    }

    /**
     * Tests using an unregistered before-filter
     */
    public function testUsingUnregisteredBeforeFilter()
    {
        $this->setExpectedException("RDev\\Models\\Web\\Routing\\Exceptions\\InvalidFilterException");
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
     * @param array $beforeFilters The list of before-filters to run
     * @param array $afterFilters The list of after-filters to run
     */
    private function setupRequest($httpMethod, $path, $controllerName, $controllerMethod, array $beforeFilters = [],
                                  array $afterFilters = [])
    {
        $options = [
            "controller" => "$controllerName@$controllerMethod"
        ];

        if(count($beforeFilters) > 0)
        {
            $options["before"] = $beforeFilters;
        }

        if(count($afterFilters) > 0)
        {
            $options["after"] = $afterFilters;
        }

        $_SERVER["REQUEST_METHOD"] = $httpMethod;
        $configArray = [
            "routes" => [
                new Route([Web\Request::METHOD_DELETE], $path, $options),
                new Route([Web\Request::METHOD_GET], $path, $options),
                new Route([Web\Request::METHOD_POST], $path, $options),
                new Route([Web\Request::METHOD_PUT], $path, $options)
            ]
        ];
        $this->router = new Router(new Web\HTTPConnection, $configArray);
    }
} 
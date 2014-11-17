<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Tests the routes
 */
namespace RDev\Routing;
use RDev\HTTP;

class RoutesTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Routes The routes to use in tests */
    private $routes = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->routes = new Routes();
    }

    /**
     * Tests adding a route
     */
    public function testAdd()
    {
        $route = new Route(HTTP\Request::METHOD_GET, "/users", ["controller" => "foo@bar"]);
        $this->routes->add($route);
        $this->assertSame([$route], $this->routes->get(HTTP\Request::METHOD_GET));
    }

    /**
     * Tests getting a route
     */
    public function testGet()
    {
        $getRoute = new Route(HTTP\Request::METHOD_GET, "/users", ["controller" => "foo@bar"]);
        $postRoute = new Route(HTTP\Request::METHOD_POST, "/users", ["controller" => "foo@bar"]);
        $expectedRoutes = [];

        foreach(Routes::getMethods() as $method)
        {
            $expectedRoutes[$method] = [];
        }

        $expectedRoutes[HTTP\Request::METHOD_GET][] = $getRoute;
        $expectedRoutes[HTTP\Request::METHOD_POST][] = $postRoute;
        $this->routes->add($getRoute);
        $this->routes->add($postRoute);
        $this->assertSame($expectedRoutes, $this->routes->get());
        $this->assertSame([$getRoute], $this->routes->get(HTTP\Request::METHOD_GET));
        $this->assertSame([$postRoute], $this->routes->get(HTTP\Request::METHOD_POST));
    }

    /**
     * Tests getting an invalid method's routes
     */
    public function testGettingInvalidMethodRoutes()
    {
        $this->assertEquals([], $this->routes->get("methodThatDoeNotExist"));
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
        $this->routes->add($expectedRoute);
        $this->assertSame($expectedRoute, $this->routes->getNamedRoute("blah"));
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
        $this->routes->get($path, $options);
        $this->assertNull($this->routes->getNamedRoute("blah"));
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
        $this->routes->add($deleteRoute);
        $this->routes->add($getRoute);
        $this->routes->add($postRoute);
        $this->routes->add($putRoute);
        $this->routes->add($headRoute);
        $this->routes->add($optionsRoute);
        $this->routes->add($patchRoute);
        $allRoutes = $this->routes->get();
        $this->assertSame([$deleteRoute], $allRoutes[HTTP\Request::METHOD_DELETE]);
        $this->assertSame([$getRoute], $allRoutes[HTTP\Request::METHOD_GET]);
        $this->assertSame([$postRoute], $allRoutes[HTTP\Request::METHOD_POST]);
        $this->assertSame([$putRoute], $allRoutes[HTTP\Request::METHOD_PUT]);
        $this->assertSame([$headRoute], $allRoutes[HTTP\Request::METHOD_HEAD]);
        $this->assertSame([$optionsRoute], $allRoutes[HTTP\Request::METHOD_OPTIONS]);
        $this->assertSame([$patchRoute], $allRoutes[HTTP\Request::METHOD_PATCH]);
    }

    /**
     * Tests getting routes for a method that does not have any
     */
    public function testGettingRoutesForMethodThatDoesNotHaveAny()
    {
        $this->assertEquals([], $this->routes->get(HTTP\Request::METHOD_GET));
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
        $this->routes->add($getRoute);
        $getRoutes = $this->routes->get(HTTP\Request::METHOD_GET);
        $this->assertSame([$getRoute], $getRoutes);
    }
}
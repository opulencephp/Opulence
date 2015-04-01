<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the routes
 */
namespace RDev\HTTP\Routing\Routes;
use RDev\HTTP\Requests\Request;

class RouteCollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var RouteCollection The routes to use in tests */
    private $collection = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->collection = new RouteCollection();
    }

    /**
     * Tests adding a route
     */
    public function testAdd()
    {
        $route = new Route(Request::METHOD_GET, "/users", ["controller" => "foo@bar"]);
        $this->collection->add($route);
        $this->assertSame([$route], $this->collection->get(Request::METHOD_GET));
    }

    /**
     * Tests getting a route
     */
    public function testGet()
    {
        $getRoute = new Route(Request::METHOD_GET, "/users", ["controller" => "foo@bar"]);
        $postRoute = new Route(Request::METHOD_POST, "/users", ["controller" => "foo@bar"]);
        $expectedRoutes = [];

        foreach(RouteCollection::getMethods() as $method)
        {
            $expectedRoutes[$method] = [];
        }

        $expectedRoutes[Request::METHOD_GET][] = $getRoute;
        $expectedRoutes[Request::METHOD_POST][] = $postRoute;
        $this->collection->add($getRoute);
        $this->collection->add($postRoute);
        $this->assertSame($expectedRoutes, $this->collection->get());
        $this->assertSame([$getRoute], $this->collection->get(Request::METHOD_GET));
        $this->assertSame([$postRoute], $this->collection->get(Request::METHOD_POST));
    }

    /**
     * Tests getting an invalid method's routes
     */
    public function testGettingInvalidMethodRoutes()
    {
        $this->assertEquals([], $this->collection->get("methodThatDoeNotExist"));
    }

    /**
     * Tests getting a named route
     */
    public function testGettingNamedRoute()
    {
        $path = "/foo";
        $options = [
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@noParameters",
            "name" => "blah"
        ];
        $expectedRoute = new Route(Request::METHOD_GET, $path, $options);
        $this->collection->add($expectedRoute);
        $this->assertSame($expectedRoute, $this->collection->getNamedRoute("blah"));
    }

    /**
     * Tests getting a non-existent named route
     */
    public function testGettingNonExistentNamedRoute()
    {
        $path = "/foo";
        $options = [
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@noParameters"
        ];
        $route = new Route(Request::METHOD_GET, $path, $options);
        $this->collection->add($route);
        $this->assertNull($this->collection->getNamedRoute("blah"));
    }

    /**
     * Tests getting the routes
     */
    public function testGettingRoutes()
    {
        $path = "/foo";
        $options = [
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@noParameters"
        ];
        $deleteRoute = new Route(Request::METHOD_DELETE, $path, $options);
        $getRoute = new Route(Request::METHOD_GET, $path, $options);
        $postRoute = new Route(Request::METHOD_POST, $path, $options);
        $putRoute = new Route(Request::METHOD_PUT, $path, $options);
        $headRoute = new Route(Request::METHOD_HEAD, $path, $options);
        $optionsRoute = new Route(Request::METHOD_OPTIONS, $path, $options);
        $patchRoute = new Route(Request::METHOD_PATCH, $path, $options);
        $this->collection->add($deleteRoute);
        $this->collection->add($getRoute);
        $this->collection->add($postRoute);
        $this->collection->add($putRoute);
        $this->collection->add($headRoute);
        $this->collection->add($optionsRoute);
        $this->collection->add($patchRoute);
        $allRoutes = $this->collection->get();
        $this->assertSame([$deleteRoute], $allRoutes[Request::METHOD_DELETE]);
        $this->assertSame([$getRoute], $allRoutes[Request::METHOD_GET]);
        $this->assertSame([$postRoute], $allRoutes[Request::METHOD_POST]);
        $this->assertSame([$putRoute], $allRoutes[Request::METHOD_PUT]);
        $this->assertSame([$headRoute], $allRoutes[Request::METHOD_HEAD]);
        $this->assertSame([$optionsRoute], $allRoutes[Request::METHOD_OPTIONS]);
        $this->assertSame([$patchRoute], $allRoutes[Request::METHOD_PATCH]);
    }

    /**
     * Tests getting routes for a method that does not have any
     */
    public function testGettingRoutesForMethodThatDoesNotHaveAny()
    {
        $this->assertEquals([], $this->collection->get(Request::METHOD_GET));
    }

    /**
     * Tests getting a specific method's routes
     */
    public function testGettingSpecificMethodRoutes()
    {
        $path = "/foo";
        $options = [
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@noParameters"
        ];
        $getRoute = new Route(Request::METHOD_GET, $path, $options);
        $this->collection->add($getRoute);
        $getRoutes = $this->collection->get(Request::METHOD_GET);
        $this->assertSame([$getRoute], $getRoutes);
    }
}
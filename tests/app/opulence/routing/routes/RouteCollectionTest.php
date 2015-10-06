<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the routes
 */
namespace Opulence\Routing\Routes;

use Closure;
use Opulence\HTTP\Requests\Request;
use Opulence\Tests\Routing\Mocks\Controller as MockController;

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
        $route = new ParsedRoute(new Route(Request::METHOD_GET, "/users", "foo@bar"));
        $this->collection->add($route);
        $this->assertSame([$route], $this->collection->get(Request::METHOD_GET));
    }

    /**
     * Tests deep cloning
     */
    public function testDeepCloning()
    {
        $route = new ParsedRoute(new Route(Request::METHOD_GET, "/users", "foo@bar"));
        $this->collection->add($route);
        $clonedCollection = clone $this->collection;
        $this->assertNotSame($route, $clonedCollection->get(Request::METHOD_GET)[0]);
    }

    /**
     * Tests getting a route
     */
    public function testGet()
    {
        $getRoute = new ParsedRoute(new Route(Request::METHOD_GET, "/users", "foo@bar"));
        $postRoute = new ParsedRoute(new Route(Request::METHOD_POST, "/users", "foo@bar"));
        $expectedRoutes = [];

        foreach (RouteCollection::getMethods() as $method) {
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
        $controller = MockController::class . "@@noParameters";
        $options = [
            "name" => "blah"
        ];
        $expectedRoute = new ParsedRoute(new Route(Request::METHOD_GET, $path, $controller, $options));
        $this->collection->add($expectedRoute);
        $this->assertSame($expectedRoute, $this->collection->getNamedRoute("blah"));
    }

    /**
     * Tests getting a non-existent named route
     */
    public function testGettingNonExistentNamedRoute()
    {
        $path = "/foo";
        $route = new ParsedRoute(new Route(Request::METHOD_GET, $path, MockController::class . "@@noParameters"));
        $this->collection->add($route);
        $this->assertNull($this->collection->getNamedRoute("blah"));
    }

    /**
     * Tests getting the routes
     */
    public function testGettingRoutes()
    {
        $path = "/foo";
        $controller = MockController::class . "@@noParameters";
        $deleteRoute = new ParsedRoute(new Route(Request::METHOD_DELETE, $path, $controller));
        $getRoute = new ParsedRoute(new Route(Request::METHOD_GET, $path, $controller));
        $postRoute = new ParsedRoute(new Route(Request::METHOD_POST, $path, $controller));
        $putRoute = new ParsedRoute(new Route(Request::METHOD_PUT, $path, $controller));
        $headRoute = new ParsedRoute(new Route(Request::METHOD_HEAD, $path, $controller));
        $optionsRoute = new ParsedRoute(new Route(Request::METHOD_OPTIONS, $path, $controller));
        $patchRoute = new ParsedRoute(new Route(Request::METHOD_PATCH, $path, $controller));
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
        $getRoute = new ParsedRoute(new Route(Request::METHOD_GET, $path, MockController::class . "@@noParameters"));
        $this->collection->add($getRoute);
        $getRoutes = $this->collection->get(Request::METHOD_GET);
        $this->assertSame([$getRoute], $getRoutes);
    }

    /**
     * Tests that serializing works with a controller class
     */
    public function testSerializingWorksWithControllerClass()
    {
        $route = new Route("get", "/", "foo@bar");
        $parsedRoute = new ParsedRoute($route);
        $this->collection->add($parsedRoute);
        $serializedCollection = serialize($this->collection);
        $unserializedCollection = unserialize($serializedCollection);
        /** @var ParsedRoute $unserializedRoute */
        $unserializedRoute = $unserializedCollection->get("get")[0];
        $this->assertEquals("foo@bar", $unserializedRoute->getController());
    }

    /**
     * Tests that serializing works with controller classes
     */
    public function testSerializingWorksWithControllerClosure()
    {
        $route = new Route("get", "/", function () {
            return "foo";
        });
        $parsedRoute = new ParsedRoute($route);
        $this->collection->add($parsedRoute);
        $serializedCollection = serialize($this->collection);
        $unserializedCollection = unserialize($serializedCollection);
        /** @var ParsedRoute $unserializedRoute */
        $unserializedRoute = $unserializedCollection->get("get")[0];
        $this->assertInstanceOf(Closure::class, $unserializedRoute->getController());
        $this->assertEquals("foo", call_user_func($unserializedRoute->getController()));
    }
}
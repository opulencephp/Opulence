<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Routing\Tests\Routes;

use Closure;
use Opulence\Http\Requests\RequestMethods;
use Opulence\Routing\Routes\ParsedRoute;
use Opulence\Routing\Routes\Route;
use Opulence\Routing\Routes\RouteCollection;
use Opulence\Routing\Tests\Mocks\Controller as MockController;

/**
 * Tests the routes
 */
class RouteCollectionTest extends \PHPUnit\Framework\TestCase
{
    /** @var RouteCollection The routes to use in tests */
    private $collection = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->collection = new RouteCollection();
    }

    /**
     * Tests adding a route
     */
    public function testAdd() : void
    {
        $route = new ParsedRoute(new Route(RequestMethods::GET, '/users', 'foo@bar'));
        $this->collection->add($route);
        $this->assertSame([$route], $this->collection->get(RequestMethods::GET));
    }

    /**
     * Tests deep cloning
     */
    public function testDeepCloning() : void
    {
        $route = new ParsedRoute(new Route(RequestMethods::GET, '/users', 'foo@bar'));
        $this->collection->add($route);
        $clonedCollection = clone $this->collection;
        $this->assertNotSame($route, $clonedCollection->get(RequestMethods::GET)[0]);
    }

    /**
     * Tests getting a route
     */
    public function testGet() : void
    {
        $getRoute = new ParsedRoute(new Route(RequestMethods::GET, '/users', 'foo@bar'));
        $postRoute = new ParsedRoute(new Route(RequestMethods::POST, '/users', 'foo@bar'));
        $expectedRoutes = [];

        foreach (RouteCollection::getMethods() as $method) {
            $expectedRoutes[$method] = [];
        }

        $expectedRoutes[RequestMethods::GET][] = $getRoute;
        $expectedRoutes[RequestMethods::POST][] = $postRoute;
        $this->collection->add($getRoute);
        $this->collection->add($postRoute);
        $this->assertSame($expectedRoutes, $this->collection->get());
        $this->assertSame([$getRoute], $this->collection->get(RequestMethods::GET));
        $this->assertSame([$postRoute], $this->collection->get(RequestMethods::POST));
    }

    /**
     * Tests getting an invalid method's routes
     */
    public function testGettingInvalidMethodRoutes() : void
    {
        $this->assertEquals([], $this->collection->get('methodThatDoeNotExist'));
    }

    /**
     * Tests getting a named route
     */
    public function testGettingNamedRoute() : void
    {
        $path = '/foo';
        $controller = MockController::class . '@@noParameters';
        $options = [
            'name' => 'blah'
        ];
        $expectedRoute = new ParsedRoute(new Route(RequestMethods::GET, $path, $controller, $options));
        $this->collection->add($expectedRoute);
        $this->assertSame($expectedRoute, $this->collection->getNamedRoute('blah'));
    }

    /**
     * Tests getting a non-existent named route
     */
    public function testGettingNonExistentNamedRoute() : void
    {
        $path = '/foo';
        $route = new ParsedRoute(new Route(RequestMethods::GET, $path, MockController::class . '@@noParameters'));
        $this->collection->add($route);
        $this->assertNull($this->collection->getNamedRoute('blah'));
    }

    /**
     * Tests getting the routes
     */
    public function testGettingRoutes() : void
    {
        $path = '/foo';
        $controller = MockController::class . '@@noParameters';
        $deleteRoute = new ParsedRoute(new Route(RequestMethods::DELETE, $path, $controller));
        $getRoute = new ParsedRoute(new Route(RequestMethods::GET, $path, $controller));
        $postRoute = new ParsedRoute(new Route(RequestMethods::POST, $path, $controller));
        $putRoute = new ParsedRoute(new Route(RequestMethods::PUT, $path, $controller));
        $headRoute = new ParsedRoute(new Route(RequestMethods::HEAD, $path, $controller));
        $optionsRoute = new ParsedRoute(new Route(RequestMethods::OPTIONS, $path, $controller));
        $patchRoute = new ParsedRoute(new Route(RequestMethods::PATCH, $path, $controller));
        $this->collection->add($deleteRoute);
        $this->collection->add($getRoute);
        $this->collection->add($postRoute);
        $this->collection->add($putRoute);
        $this->collection->add($headRoute);
        $this->collection->add($optionsRoute);
        $this->collection->add($patchRoute);
        $allRoutes = $this->collection->get();
        $this->assertSame([$deleteRoute], $allRoutes[RequestMethods::DELETE]);
        $this->assertSame([$getRoute], $allRoutes[RequestMethods::GET]);
        $this->assertSame([$postRoute], $allRoutes[RequestMethods::POST]);
        $this->assertSame([$putRoute], $allRoutes[RequestMethods::PUT]);
        $this->assertSame([$headRoute], $allRoutes[RequestMethods::HEAD]);
        $this->assertSame([$optionsRoute], $allRoutes[RequestMethods::OPTIONS]);
        $this->assertSame([$patchRoute], $allRoutes[RequestMethods::PATCH]);
    }

    /**
     * Tests getting routes for a method that does not have any
     */
    public function testGettingRoutesForMethodThatDoesNotHaveAny() : void
    {
        $this->assertEquals([], $this->collection->get(RequestMethods::GET));
    }

    /**
     * Tests getting a specific method's routes
     */
    public function testGettingSpecificMethodRoutes() : void
    {
        $path = '/foo';
        $getRoute = new ParsedRoute(new Route(RequestMethods::GET, $path, MockController::class . '@noParameters'));
        $this->collection->add($getRoute);
        $getRoutes = $this->collection->get(RequestMethods::GET);
        $this->assertSame([$getRoute], $getRoutes);
    }

    /**
     * Tests that serializing works with a controller class
     */
    public function testSerializingWorksWithControllerClass() : void
    {
        $route = new Route('get', '/', 'foo@bar');
        $parsedRoute = new ParsedRoute($route);
        $this->collection->add($parsedRoute);
        $serializedCollection = serialize($this->collection);
        $unserializedCollection = unserialize($serializedCollection);
        /** @var ParsedRoute $unserializedRoute */
        $unserializedRoute = $unserializedCollection->get('get')[0];
        $this->assertEquals('foo@bar', $unserializedRoute->getController());
    }

    /**
     * Tests that serializing works with controller classes
     */
    public function testSerializingWorksWithControllerClosure() : void
    {
        $route = new Route('get', '/', function () {
            return 'foo';
        }, ['name' => 'foo']);
        $parsedRoute = new ParsedRoute($route);
        $this->collection->add($parsedRoute);
        $serializedCollection = serialize($this->collection);
        $unserializedCollection = unserialize($serializedCollection);
        /** @var ParsedRoute $unserializedRoute */
        $unserializedRoute = $unserializedCollection->get('get')[0];
        $this->assertInstanceOf(Closure::class, $unserializedRoute->getController());
        $this->assertEquals('foo', call_user_func($unserializedRoute->getController()));
    }
}

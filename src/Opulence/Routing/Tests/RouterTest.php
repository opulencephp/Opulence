<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Routing\Tests;

use InvalidArgumentException;
use Opulence\Http\HttpException;
use Opulence\Http\Requests\Request;
use Opulence\Http\Requests\RequestMethods;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Controller;
use Opulence\Routing\Dispatchers\IDependencyResolver;
use Opulence\Routing\Dispatchers\MiddlewarePipeline;
use Opulence\Routing\Dispatchers\RouteDispatcher;
use Opulence\Routing\Router;
use Opulence\Routing\Routes\Compilers\Compiler;
use Opulence\Routing\Routes\Compilers\Matchers\HostMatcher;
use Opulence\Routing\Routes\Compilers\Matchers\PathMatcher;
use Opulence\Routing\Routes\Compilers\Matchers\SchemeMatcher;
use Opulence\Routing\Routes\Compilers\Parsers\Parser;
use Opulence\Routing\Routes\Route;
use Opulence\Routing\Routes\RouteCollection;
use Opulence\Routing\Tests\Mocks\Controller as MockController;
use Opulence\Routing\Tests\Mocks\NonOpulenceController;
use Opulence\Routing\Tests\Mocks\Router as MockRouter;

/**
 * Tests the router
 */
class RouterTest extends \PHPUnit\Framework\TestCase
{
    /** @var Router The router to use in tests */
    private $router = null;
    /** @var Parser The parser to use */
    private $parser = null;
    /** @var Compiler The compiler to use */
    private $compiler = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        /** @var IDependencyResolver|\PHPUnit_Framework_MockObject_MockObject $dependencyResolver */
        $dependencyResolver = $this->createMock(IDependencyResolver::class);
        $dependencyResolver->expects($this->any())
            ->method('resolve')
            ->willReturnCallback(function () {
                $interface = func_get_arg(0);

                switch ($interface) {
                    case NonOpulenceController::class:
                        return new NonOpulenceController(Request::createFromGlobals());
                    default:
                        throw new InvalidArgumentException("Interface $interface is not setup in mock");
                }
            });
        $routeMatchers = [
            new PathMatcher(),
            new HostMatcher(),
            new SchemeMatcher()
        ];
        $this->parser = new Parser();
        $this->compiler = new Compiler($routeMatchers);
        $this->router = new Router(
            new RouteDispatcher($dependencyResolver, new MiddlewarePipeline()),
            $this->compiler,
            $this->parser
        );
    }

    /**
     * Tests that adding a route returns the instance of the route
     */
    public function testAddingRouteReturnsInstance() : void
    {
        $path = '/foo';
        $controller = 'foo@bar';
        $this->assertInstanceOf(Route::class,
            $this->router->addRoute(new Route(RequestMethods::GET, $path, $controller)));
        $this->assertInstanceOf(Route::class, $this->router->any($path, $controller)[0]);
        $this->assertInstanceOf(Route::class, $this->router->delete($path, $controller));
        $this->assertInstanceOf(Route::class, $this->router->get($path, $controller));
        $this->assertInstanceOf(Route::class, $this->router->head($path, $controller));
        $this->assertInstanceOf(Route::class, $this->router->multiple([RequestMethods::GET], $path, $controller)[0]);
        $this->assertInstanceOf(Route::class, $this->router->options($path, $controller));
        $this->assertInstanceOf(Route::class, $this->router->patch($path, $controller));
        $this->assertInstanceOf(Route::class, $this->router->post($path, $controller));
        $this->assertInstanceOf(Route::class, $this->router->put($path, $controller));
    }

    /**
     * Tests adding routes through their specific methods
     */
    public function testAddingRoutesThroughTheirSpecificMethods() : void
    {
        $path = '/foo';
        $controller = 'foo@bar';

        foreach (RouteCollection::getMethods() as $method) {
            $this->router->{strtolower($method)}($path, $controller);
            $expectedRoute = $this->parser->parse(new Route($method, $path, $controller));
            $this->assertEquals([$expectedRoute], $this->router->getRouteCollection()->get($method));
        }
    }

    /**
     * Tests getting the matched route when there is none
     */
    public function testGettingMatchedRouteWhenThereIsNone() : void
    {
        $this->assertNull($this->router->getMatchedRoute());
    }

    /**
     * Tests that group settings are getting applied to returned routes
     */
    public function testGroupSettingsAreGettingAppliedToReturnedRoutes() : void
    {
        $getRoute = null;

        $this->router->group(['path' => '/foo/:id', 'vars' => ['id' => "\d+"]],
            function (Router $router) use (&$getRoute) {
                $getRoute = $router->get('/foo', 'foo@bar');
            });
        $this->assertSame($getRoute, $this->router->getRouteCollection()->get(RequestMethods::GET)[0]);
    }

    /**
     * Tests a group with variable regexes
     */
    public function testGroupWithVariableRegexes() : void
    {
        $this->router->group(['path' => '/users/:userId', 'vars' => ['id' => "\d+"]], function (Router $router) {
            $router->get('/foo', 'foo@bar');
            $router->post('/foo', 'foo@bar');
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(RequestMethods::POST);
        $this->assertEquals("\d+", $getRoutes[0]->getVarRegex('id'));
        $this->assertEquals("\d+", $postRoutes[0]->getVarRegex('id'));
    }

    /**
     * Tests that grouped routes do not overwrite any controller settings for a closure route
     */
    public function testGroupedRoutesDoNotOverwriteControllerSettingsInClosureRoute() : void
    {
        $controller = function () {
            return 'Foo';
        };
        $closureRoute = $this->router->get('/foo', $controller);
        $this->router->group(['controllerNamespace' => 'MyApp'], function (Router $router) {
            $router->get('bar', 'MyController@index');
        });
        $this->assertTrue($closureRoute->usesCallable());
        $this->assertSame($controller, $closureRoute->getController());
    }

    /**
     * Tests grouping routes
     */
    public function testGroupingRoutes() : void
    {
        $groupOptions = [
            'path' => '/foo',
            'middleware' => ['foo1', 'foo2']
        ];
        $this->router->group($groupOptions, function (Router $router) {
            $controller = MockController::class . '@noParameters';
            $router->addRoute(new Route(RequestMethods::GET, '/bar', $controller));
            $router->delete('/blah', $controller);
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        /** @var Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRouteCollection()->get(RequestMethods::DELETE);
        $this->assertEquals('/foo/bar', $getRoutes[0]->getRawPath());
        $this->assertEquals(['foo1', 'foo2'], $getRoutes[0]->getMiddleware());
        $this->assertEquals('/foo/blah', $deleteRoutes[0]->getRawPath());
        $this->assertEquals(['foo1', 'foo2'], $deleteRoutes[0]->getMiddleware());
    }

    /**
     * Tests grouping the routes and then adding another route
     */
    public function testGroupingRoutesThenAddingAnotherRoute() : void
    {
        $controller = MockController::class . '@noParameters';
        $routeOptions = [
            'middleware' => ['foo3', 'foo4']
        ];
        $groupOptions = [
            'path' => '/foo',
            'middleware' => ['foo1', 'foo2']
        ];
        $this->router->group($groupOptions, function (Router $router) use ($controller, $routeOptions) {
            $router->addRoute(new Route(RequestMethods::GET, '/bar', $controller, $routeOptions));
            $router->delete('/blah', $controller, $routeOptions);
        });
        $this->router->get('/asdf', $controller, $routeOptions);
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        /** @var Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRouteCollection()->get(RequestMethods::DELETE);
        $this->assertEquals('/foo/bar', $getRoutes[0]->getRawPath());
        $this->assertEquals(['foo1', 'foo2', 'foo3', 'foo4'], $getRoutes[0]->getMiddleware());
        $this->assertEquals('/asdf', $getRoutes[1]->getRawPath());
        $this->assertEquals(['foo3', 'foo4'], $getRoutes[1]->getMiddleware());
        $this->assertEquals('/foo/blah', $deleteRoutes[0]->getRawPath());
        $this->assertEquals(['foo1', 'foo2', 'foo3', 'foo4'], $deleteRoutes[0]->getMiddleware());
    }

    /**
     * Tests grouping routes that use a string for the middleware
     */
    public function testGroupingRoutesWithStringMiddleware() : void
    {
        $controller = MockController::class . '@noParameters';
        $routeOptions = [
            'middleware' => 'foo2'
        ];
        $groupOptions = [
            'path' => '/foo',
            'middleware' => 'foo1'
        ];
        $this->router->group($groupOptions, function (Router $router) use ($controller, $routeOptions) {
            $router->addRoute(new Route(RequestMethods::GET, '/bar', $controller, $routeOptions));
            $router->delete('/blah', $controller, $routeOptions);
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        /** @var Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRouteCollection()->get(RequestMethods::DELETE);
        $this->assertEquals(['foo1', 'foo2'], $getRoutes[0]->getMiddleware());
        $this->assertEquals(['foo1', 'foo2'], $deleteRoutes[0]->getMiddleware());
    }

    /**
     * Tests that the matched controller is null before routing
     */
    public function testMatchedControllerIsNullBeforeRouting() : void
    {
        $this->assertNull($this->router->getMatchedController());
    }

    /**
     * Tests mixing HTTPS on nested groups
     */
    public function testMixingHttpsOnNestedGroups() : void
    {
        $this->router->group(['https' => true], function (Router $router) {
            $router->group(['https' => false], function (Router $router) {
                $router->get('/foo', 'foo@bar');
                $router->post('/foo', 'foo@bar');
            });
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(RequestMethods::POST);
        $this->assertTrue($getRoutes[0]->isSecure());
        $this->assertTrue($postRoutes[0]->isSecure());
    }

    /**
     * Tests nested grouped routes
     */
    public function testNestedGroupedRoutes() : void
    {
        $outerGroupOptions = [
            'path' => '/foo',
            'controllerNamespace' => 'Opulence\\Routing\\Tests',
            'middleware' => ['foo1', 'foo2']
        ];
        $outerRouteController = 'Mocks\\Controller@noParameters';
        $innerRouteController = 'Controller@noParameters';
        $this->router->group($outerGroupOptions,
            function (Router $router) use ($outerRouteController, $innerRouteController) {
                $router->addRoute(new Route(RequestMethods::GET, '/bar', $outerRouteController));
                $router->delete('/blah', $outerRouteController);
                $innerGroupOptions = [
                    'path' => '/asdf',
                    'controllerNamespace' => 'Mocks',
                    'middleware' => ['foo3', 'foo4']
                ];
                $router->group($innerGroupOptions, function (Router $router) use ($innerRouteController) {
                    $router->get('/jkl', $innerRouteController);
                });
            });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        /** @var Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRouteCollection()->get(RequestMethods::DELETE);
        $this->assertEquals('/foo/bar', $getRoutes[0]->getRawPath());
        $this->assertEquals(MockController::class, $getRoutes[0]->getControllerName());
        $this->assertEquals(['foo1', 'foo2'], $getRoutes[0]->getMiddleware());
        $this->assertEquals('/foo/asdf/jkl', $getRoutes[1]->getRawPath());
        $this->assertEquals(MockController::class, $getRoutes[1]->getControllerName());
        $this->assertEquals(['foo1', 'foo2', 'foo3', 'foo4'], $getRoutes[1]->getMiddleware());
        $this->assertEquals('/foo/blah', $deleteRoutes[0]->getRawPath());
        $this->assertEquals(MockController::class, $deleteRoutes[0]->getControllerName());
        $this->assertEquals(['foo1', 'foo2'], $deleteRoutes[0]->getMiddleware());
    }

    /**
     * Tests that nested groups with variable regexes overwrite one another
     */
    public function testNestingGroupVariableRegexesOverwriteOneAnother() : void
    {
        $this->router->group(['path' => '/users/:userId', 'vars' => ['id' => "\d*"]], function (Router $router) {
            $router->get('/foo', 'foo@bar');
            // This route's variable regex should take precedence
            $router->get('/bam', 'foo@bam', ['vars' => ['id' => "\w+"]]);

            $router->group(['path' => '/bar', 'vars' => ['id' => "\d+"]], function (Router $router) {
                $router->get('/baz', 'bar@baz');
            });
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        $this->assertEquals("\d*", $getRoutes[0]->getVarRegex('id'));
        $this->assertEquals("\w+", $getRoutes[1]->getVarRegex('id'));
        $this->assertEquals("\d+", $getRoutes[2]->getVarRegex('id'));
    }

    /**
     * Tests routing for any method
     */
    public function testRoutingAnyMethod() : void
    {
        $controller = MockController::class . '@noParameters';
        $this->router->any('/foo', $controller);
        $allRoutes = $this->router->getRouteCollection()->get();
        $this->assertCount(1, $allRoutes[RequestMethods::GET]);
        $this->assertCount(1, $allRoutes[RequestMethods::POST]);
        $this->assertCount(1, $allRoutes[RequestMethods::DELETE]);
        $this->assertCount(1, $allRoutes[RequestMethods::PUT]);
    }

    /**
     * Tests routing a DELETE request
     */
    public function testRoutingDeleteRequest() : void
    {
        $this->doTestForHttpMethod(RequestMethods::DELETE);
    }

    /**
     * Tests routing a GET request
     */
    public function testRoutingGetRequest() : void
    {
        $this->doTestForHttpMethod(RequestMethods::GET);
    }

    /**
     * Tests routing a HEAD request
     */
    public function testRoutingHeadRequest() : void
    {
        $this->doTestForHttpMethod(RequestMethods::HEAD);
    }

    /**
     * Tests routing a missing path
     */
    public function testRoutingMissingPath() : void
    {
        $this->expectException(HttpException::class);
        $request = new Request([], [], [], [
            'REQUEST_METHOD' => RequestMethods::GET,
            'REQUEST_URI' => '/foo/'
        ], [], []);
        $this->router->route($request);
    }

    /**
     * Tests routing for multiple methods
     */
    public function testRoutingMultipleMethods() : void
    {
        $controller = MockController::class . '@noParameters';
        $this->router->multiple([RequestMethods::GET, RequestMethods::POST], '/foo', $controller);
        $allRoutes = $this->router->getRouteCollection()->get();
        $this->assertCount(1, $allRoutes[RequestMethods::GET]);
        $this->assertCount(1, $allRoutes[RequestMethods::POST]);
        $this->assertCount(0, $allRoutes[RequestMethods::DELETE]);
        $this->assertCount(0, $allRoutes[RequestMethods::PUT]);
        $this->assertCount(0, $allRoutes[RequestMethods::HEAD]);
        $this->assertCount(0, $allRoutes[RequestMethods::OPTIONS]);
        $this->assertCount(0, $allRoutes[RequestMethods::PATCH]);
    }

    /**
     * Tests routing a OPTIONS request
     */
    public function testRoutingOptionsRequest() : void
    {
        $this->doTestForHttpMethod(RequestMethods::OPTIONS);
    }

    /**
     * Tests routing a PATCH request
     */
    public function testRoutingPatchRequest() : void
    {
        $this->doTestForHttpMethod(RequestMethods::PATCH);
    }

    /**
     * Tests routing a POST request
     */
    public function testRoutingPostRequest() : void
    {
        $this->doTestForHttpMethod(RequestMethods::POST);
    }

    /**
     * Tests routing a PUT request
     */
    public function testRoutingPutRequest() : void
    {
        $this->doTestForHttpMethod(RequestMethods::PUT);
    }

    /**
     * Tests routing to a non-Opulence controller
     */
    public function testRoutingToNonOpulenceController() : void
    {
        $controller = NonOpulenceController::class . '@index';
        $this->router->get('/foo/:id', $controller);
        $server = [
            'REQUEST_METHOD' => RequestMethods::GET,
            'REQUEST_URI' => '/foo/123',
            'HTTP_HOST' => ''
        ];
        $request = new Request([], [], [], $server, [], []);
        $response = $this->router->route($request);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertInstanceOf(NonOpulenceController::class, $this->router->getMatchedController());
        $this->assertEquals('Id: 123', $response->getContent());
    }

    /**
     * Tests a secure group
     */
    public function testSecureGroup() : void
    {
        $this->router->group(['https' => true], function (Router $router) {
            $router->get('/foo', 'foo@bar');
            $router->post('/foo', 'foo@bar');
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(RequestMethods::POST);
        $this->assertTrue($getRoutes[0]->isSecure());
        $this->assertTrue($postRoutes[0]->isSecure());
    }

    /**
     * Tests setting the route collection
     */
    public function testSettingRouteCollection() : void
    {
        /** @var RouteCollection $collection */
        $collection = $this->createMock(RouteCollection::class);
        $this->router->setRouteCollection($collection);
        $this->assertSame($collection, $this->router->getRouteCollection());
    }

    /**
     * Tests specifying a group host
     */
    public function testSpecifyingGroupHost() : void
    {
        $this->router->group(['host' => 'google.com'], function (Router $router) {
            $router->get('/foo', 'foo@bar');
            $router->post('/foo', 'foo@bar');
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(RequestMethods::POST);
        $this->assertEquals('google.com', $getRoutes[0]->getRawHost());
        $this->assertEquals('google.com', $postRoutes[0]->getRawHost());
    }

    /**
     * Tests specifying a namespace prefix
     */
    public function testSpecifyingNamespacePrefix() : void
    {
        $this->router->group(['controllerNamespace' => 'MyApp\\Controllers\\'], function (Router $router) {
            $router->get('/foo', 'ControllerA@myMethod');
            $router->post('/foo', 'ControllerB@myMethod');
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(RequestMethods::POST);
        $this->assertEquals('MyApp\\Controllers\\ControllerA', $getRoutes[0]->getControllerName());
        $this->assertEquals('MyApp\\Controllers\\ControllerB', $postRoutes[0]->getControllerName());
    }

    /**
     * Tests specifying a namespace prefix with no trailing slash
     */
    public function testSpecifyingNamespacePrefixWithNoTrailingSlash() : void
    {
        $this->router->group(['controllerNamespace' => 'MyApp\\Controllers'], function () {
            $this->router->get('/foo', 'ControllerA@myMethod');
            $this->router->post('/foo', 'ControllerB@myMethod');
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(RequestMethods::POST);
        $this->assertEquals('MyApp\\Controllers\\ControllerA', $getRoutes[0]->getControllerName());
        $this->assertEquals('MyApp\\Controllers\\ControllerB', $postRoutes[0]->getControllerName());
    }

    /**
     * Tests specifying a nested group hosts
     */
    public function testSpecifyingNestedGroupHosts() : void
    {
        $this->router->group(['host' => 'google.com'], function (Router $router) {
            $router->group(['host' => 'mail.'], function (Router $router) {
                $router->get('/foo', 'foo@bar');
                $router->post('/foo', 'foo@bar');
            });
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(RequestMethods::POST);
        $this->assertEquals('mail.google.com', $getRoutes[0]->getRawHost());
        $this->assertEquals('mail.google.com', $postRoutes[0]->getRawHost());
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
    ) : void {
        $controller = "$controllerName@$controllerMethod";
        $options = [
            'host' => $rawHost
        ];

        // The mock router will return the route used rather than the output of the route controller
        // This makes testing easier
        $mockRouter = new MockRouter();
        $deleteRoute = new Route(RequestMethods::DELETE, $rawPath, $controller, $options);
        $getRoute = new Route(RequestMethods::GET, $rawPath, $controller, $options);
        $postRoute = new Route(RequestMethods::POST, $rawPath, $controller, $options);
        $putRoute = new Route(RequestMethods::PUT, $rawPath, $controller, $options);
        $headRoute = new Route(RequestMethods::HEAD, $rawPath, $controller, $options);
        $optionsRoute = new Route(RequestMethods::OPTIONS, $rawPath, $controller, $options);
        $patchRoute = new Route(RequestMethods::PATCH, $rawPath, $controller, $options);
        $mockRouter->addRoute($deleteRoute);
        $mockRouter->addRoute($getRoute);
        $mockRouter->addRoute($postRoute);
        $mockRouter->addRoute($putRoute);
        $mockRouter->addRoute($headRoute);
        $mockRouter->addRoute($optionsRoute);
        $mockRouter->addRoute($patchRoute);
        $server = [
            'REQUEST_METHOD' => $httpMethod,
            'REQUEST_URI' => $pathToRoute,
            'HTTP_HOST' => $hostToRoute
        ];
        $request = new Request([], [], [], $server, [], []);
        $routeToHandle = null;

        switch ($httpMethod) {
            case RequestMethods::DELETE:
                $routeToHandle = $deleteRoute;

                break;
            case RequestMethods::GET:
                $routeToHandle = $getRoute;

                break;
            case RequestMethods::POST:
                $routeToHandle = $postRoute;

                break;
            case RequestMethods::PUT:
                $routeToHandle = $putRoute;

                break;
            case RequestMethods::HEAD:
                $routeToHandle = $headRoute;

                break;
            case RequestMethods::OPTIONS:
                $routeToHandle = $optionsRoute;

                break;
            case RequestMethods::PATCH:
                $routeToHandle = $patchRoute;

                break;
        }

        $parsedRoute = $this->parser->parse($routeToHandle);
        $compiledRoute = $this->compiler->compile($parsedRoute, $request);
        $mockRouter->route($request);
        $this->assertEquals($compiledRoute, $mockRouter->getLastRoute());
        $this->assertEquals($compiledRoute, $mockRouter->getMatchedRoute());
        // The mock router does not actually instantiate the input controller
        // Instead, its dispatcher always sets the controller to the same object every time
        $this->assertInstanceOf(Controller::class, $mockRouter->getMatchedController());
    }

    /**
     * Tests a request with the input HTTP method
     *
     * @param string $httpMethod The HTTP method to test
     */
    private function doTestForHttpMethod($httpMethod) : void
    {
        $this->doRoute(
            $httpMethod,
            '/foo',
            '/foo',
            'google.com',
            'google.com',
            MockController::class,
            'noParameters'
        );
        $this->doRoute(
            $httpMethod,
            '/foo/:foo',
            '/foo/123',
            ':bar.google.com',
            'mail.google.com',
            MockController::class,
            'twoParameters'
        );
        $this->doRoute(
            $httpMethod,
            '/foo/:foo/:bar',
            '/foo/123/456',
            ':baz.:blah.google.com',
            'u.mail.google.com',
            MockController::class,
            'severalParameters'
        );
    }
}

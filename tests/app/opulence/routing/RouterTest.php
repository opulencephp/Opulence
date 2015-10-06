<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the router
 */
namespace Opulence\Routing;

use InvalidArgumentException;
use Opulence\HTTP\Requests\Request;
use Opulence\HTTP\Responses\Response;
use Opulence\HTTP\Responses\ResponseHeaders;
use Opulence\Routing\Dispatchers\Dispatcher;
use Opulence\Routing\Routes\Compilers\Compiler;
use Opulence\Routing\Routes\Compilers\Matchers\HostMatcher;
use Opulence\Routing\Routes\Compilers\Matchers\PathMatcher;
use Opulence\Routing\Routes\Compilers\Matchers\SchemeMatcher;
use Opulence\Routing\Routes\Compilers\Parsers\Parser;
use Opulence\Routing\Routes\Route;
use Opulence\Routing\Routes\RouteCollection;
use Opulence\IoC\Container;
use Opulence\Tests\Routing\Mocks\Controller as MockController;
use Opulence\Tests\Routing\Mocks\NonOpulenceController;
use Opulence\Tests\Routing\Mocks\Router as MockRouter;

class RouterTest extends \PHPUnit_Framework_TestCase
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
    public function setUp()
    {
        $container = new Container();
        $routeMatchers = [
            new PathMatcher(),
            new HostMatcher(),
            new SchemeMatcher()
        ];
        $this->parser = new Parser();
        $this->compiler = new Compiler($routeMatchers);
        $this->router = new Router(new Dispatcher($container), $this->compiler, $this->parser);
    }

    /**
     * Tests that adding a route returns the instance of the route
     */
    public function testAddingRouteReturnsInstance()
    {
        $path = "/foo";
        $controller = "foo@bar";
        $this->assertInstanceOf(Route::class,
            $this->router->addRoute(new Route(Request::METHOD_GET, $path, $controller)));
        $this->assertInstanceOf(Route::class, $this->router->any($path, $controller)[0]);
        $this->assertInstanceOf(Route::class, $this->router->delete($path, $controller));
        $this->assertInstanceOf(Route::class, $this->router->get($path, $controller));
        $this->assertInstanceOf(Route::class, $this->router->head($path, $controller));
        $this->assertInstanceOf(Route::class, $this->router->multiple([Request::METHOD_GET], $path, $controller)[0]);
        $this->assertInstanceOf(Route::class, $this->router->options($path, $controller));
        $this->assertInstanceOf(Route::class, $this->router->patch($path, $controller));
        $this->assertInstanceOf(Route::class, $this->router->post($path, $controller));
        $this->assertInstanceOf(Route::class, $this->router->put($path, $controller));
    }

    /**
     * Tests adding routes through their specific methods
     */
    public function testAddingRoutesThroughTheirSpecificMethods()
    {
        $path = "/foo";
        $controller = "foo@bar";

        foreach (RouteCollection::getMethods() as $method) {
            call_user_func_array([$this->router, strtolower($method)], [$path, $controller]);
            $expectedRoute = $this->parser->parse(new Route($method, $path, $controller));
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
     * Tests that group settings are getting applied to returned routes
     */
    public function testGroupSettingsAreGettingAppliedToReturnedRoutes()
    {
        $getRoute = null;

        $this->router->group(["path" => "/foo/:id", "variables" => ["id" => "\d+"]], function () use (&$getRoute) {
            $getRoute = $this->router->get("/foo", "foo@bar");
        });
        $this->assertSame($getRoute, $this->router->getRouteCollection()->get(Request::METHOD_GET)[0]);
    }

    /**
     * Tests a group with variable regexes
     */
    public function testGroupWithVariableRegexes()
    {
        $this->router->group(["path" => "/users/:userId", "variables" => ["id" => "\d+"]], function () {
            $this->router->get("/foo", "foo@bar");
            $this->router->post("/foo", "foo@bar");
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(Request::METHOD_GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(Request::METHOD_POST);
        $this->assertEquals("\d+", $getRoutes[0]->getVarRegex("id"));
        $this->assertEquals("\d+", $postRoutes[0]->getVarRegex("id"));
    }

    /**
     * Tests that grouped routes do not overwrite any controller settings for a closure route
     */
    public function testGroupedRoutesDoNotOverwriteControllerSettingsInClosureRoute()
    {
        $controller = function () {
            return "Foo";
        };
        $closureRoute = $this->router->get("/foo", $controller);
        $this->router->group(["controllerNamespace" => "MyApp"], function () {
            $this->router->get("bar", "MyController@index");
        });
        $this->assertTrue($closureRoute->usesClosure());
        $this->assertSame($controller, $closureRoute->getController());
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
        $this->router->group($groupOptions, function () {
            $controller = MockController::class . "@noParameters";
            $this->router->addRoute(new Route(Request::METHOD_GET, "/bar", $controller));
            $this->router->delete("/blah", $controller);
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
        $controller = MockController::class . "@noParameters";
        $routeOptions = [
            "middleware" => ["foo3", "foo4"]
        ];
        $groupOptions = [
            "path" => "/foo",
            "middleware" => ["foo1", "foo2"]
        ];
        $this->router->group($groupOptions, function () use ($controller, $routeOptions) {
            $this->router->addRoute(new Route(Request::METHOD_GET, "/bar", $controller, $routeOptions));
            $this->router->delete("/blah", $controller, $routeOptions);
        });
        $this->router->get("/asdf", $controller, $routeOptions);
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
        $controller = MockController::class . "@noParameters";
        $routeOptions = [
            "middleware" => "foo2"
        ];
        $groupOptions = [
            "path" => "/foo",
            "middleware" => "foo1"
        ];
        $this->router->group($groupOptions, function () use ($controller, $routeOptions) {
            $this->router->addRoute(new Route(Request::METHOD_GET, "/bar", $controller, $routeOptions));
            $this->router->delete("/blah", $controller, $routeOptions);
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(Request::METHOD_GET);
        /** @var Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRouteCollection()->get(Request::METHOD_DELETE);
        $this->assertEquals(["foo1", "foo2"], $getRoutes[0]->getMiddleware());
        $this->assertEquals(["foo1", "foo2"], $deleteRoutes[0]->getMiddleware());
    }

    /**
     * Tests specifying an invalid route controller method in the constructor
     */
    public function testInvalidMissedRouteControllerMethodInConstructor()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $parser = new Parser();
        $compiler = new Compiler([]);
        new Router(new Dispatcher(new Container()), $compiler, $parser, NonOpulenceController::class, "doesNotExist");
    }

    /**
     * Tests specifying an invalid route controller method in the setter
     */
    public function testInvalidMissedRouteControllerMethodInSetter()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->router->setMissedRouteController(NonOpulenceController::class, "doesNotExist");
    }

    /**
     * Tests specifying an invalid route controller name in the constructor
     */
    public function testInvalidMissedRouteControllerNameInConstructor()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $parser = new Parser();
        $compiler = new Compiler([]);
        new Router(new Dispatcher(new Container()), $compiler, $parser, "Class\\That\\Does\\Not\\Exist");
    }

    /**
     * Tests specifying an invalid route controller name in the setter
     */
    public function testInvalidMissedRouteControllerNameInSetter()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->router->setMissedRouteController("Class\\That\\Does\\Not\\Exist", "foo");
    }

    /**
     * Tests that the matched controller is null before routing
     */
    public function testMatchedControllerIsNullBeforeRouting()
    {
        $this->assertNull($this->router->getMatchedController());
    }

    /**
     * Tests a missed route to a non-Opulence controller
     */
    public function testMissedRouteToNonOpulenceController()
    {
        $controller = NonOpulenceController::class . "@index";
        $this->router->setMissedRouteController(NonOpulenceController::class, "customHTTPError");
        $this->router->get("/foo/:id", $controller);
        $server = [
            "REQUEST_METHOD" => Request::METHOD_GET,
            "REQUEST_URI" => "/bar",
            "HTTP_HOST" => ""
        ];
        $request = new Request([], [], [], $server, [], []);
        $response = $this->router->route($request);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertInstanceOf(NonOpulenceController::class, $this->router->getMatchedController());
        $this->assertEquals("Error: 404", $response->getContent());
    }

    /**
     * Tests mixing HTTPS on nested groups
     */
    public function testMixingHTTPSOnNestedGroups()
    {
        $this->router->group(["https" => true], function () {
            $this->router->group(["https" => false], function () {
                $this->router->get("/foo", "foo@bar");
                $this->router->post("/foo", "foo@bar");
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
            "controllerNamespace" => "Opulence\\Tests\\Routing",
            "middleware" => ["foo1", "foo2"]
        ];
        $outerRouteController = "Mocks\\Controller@noParameters";
        $innerRouteController = "Controller@noParameters";
        $this->router->group($outerGroupOptions, function () use ($outerRouteController, $innerRouteController) {
            $this->router->addRoute(new Route(Request::METHOD_GET, "/bar", $outerRouteController));
            $this->router->delete("/blah", $outerRouteController);
            $innerGroupOptions = [
                "path" => "/asdf",
                "controllerNamespace" => "Mocks",
                "middleware" => ["foo3", "foo4"]
            ];
            $this->router->group($innerGroupOptions, function () use ($innerRouteController) {
                $this->router->get("/jkl", $innerRouteController);
            });
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(Request::METHOD_GET);
        /** @var Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRouteCollection()->get(Request::METHOD_DELETE);
        $this->assertEquals("/foo/bar", $getRoutes[0]->getRawPath());
        $this->assertEquals(MockController::class, $getRoutes[0]->getControllerName());
        $this->assertEquals(["foo1", "foo2"], $getRoutes[0]->getMiddleware());
        $this->assertEquals("/foo/asdf/jkl", $getRoutes[1]->getRawPath());
        $this->assertEquals(MockController::class, $getRoutes[1]->getControllerName());
        $this->assertEquals(["foo1", "foo2", "foo3", "foo4"], $getRoutes[1]->getMiddleware());
        $this->assertEquals("/foo/blah", $deleteRoutes[0]->getRawPath());
        $this->assertEquals(MockController::class, $deleteRoutes[0]->getControllerName());
        $this->assertEquals(["foo1", "foo2"], $deleteRoutes[0]->getMiddleware());
    }

    /**
     * Tests that nested groups with variable regexes overwrite one another
     */
    public function testNestingGroupVariableRegexesOverwriteOneAnother()
    {
        $this->router->group(["path" => "/users/:userId", "variables" => ["id" => "\d*"]], function () {
            $this->router->get("/foo", "foo@bar");
            // This route's variable regex should take precedence
            $this->router->get("/bam", "foo@bam", ["variables" => ["id" => "\w+"]]);

            $this->router->group(["path" => "/bar", "variables" => ["id" => "\d+"]], function () {
                $this->router->get("/baz", "bar@baz");
            });
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(Request::METHOD_GET);
        $this->assertEquals("\d*", $getRoutes[0]->getVarRegex("id"));
        $this->assertEquals("\w+", $getRoutes[1]->getVarRegex("id"));
        $this->assertEquals("\d+", $getRoutes[2]->getVarRegex("id"));
    }

    /**
     * Tests routing for any method
     */
    public function testRoutingAnyMethod()
    {
        $controller = MockController::class . "@noParameters";
        $this->router->any("/foo", $controller);
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
        $this->assertInstanceOf(Response::class, $response);
        $this->assertInstanceOf("Opulence\\Routing\\Controller", $this->router->getMatchedController());
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
        $this->router->setMissedRouteController(MockController::class);
        $response = $this->router->route($request);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(ResponseHeaders::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals("foo", $response->getContent());
    }

    /**
     * Tests routing for multiple methods
     */
    public function testRoutingMultipleMethods()
    {
        $controller = MockController::class . "@noParameters";
        $this->router->multiple([Request::METHOD_GET, Request::METHOD_POST], "/foo", $controller);
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
     * Tests routing to a non-Opulence controller
     */
    public function testRoutingToNonOpulenceController()
    {
        $controller = NonOpulenceController::class . "@index";
        $this->router->get("/foo/:id", $controller);
        $server = [
            "REQUEST_METHOD" => Request::METHOD_GET,
            "REQUEST_URI" => "/foo/123",
            "HTTP_HOST" => ""
        ];
        $request = new Request([], [], [], $server, [], []);
        $response = $this->router->route($request);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertInstanceOf(NonOpulenceController::class, $this->router->getMatchedController());
        $this->assertEquals("Id: 123", $response->getContent());
    }

    /**
     * Tests a secure group
     */
    public function testSecureGroup()
    {
        $this->router->group(["https" => true], function () {
            $this->router->get("/foo", "foo@bar");
            $this->router->post("/foo", "foo@bar");
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(Request::METHOD_GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(Request::METHOD_POST);
        $this->assertTrue($getRoutes[0]->isSecure());
        $this->assertTrue($postRoutes[0]->isSecure());
    }

    /**
     * Tests setting the route collection
     */
    public function testSettingRouteCollection()
    {
        /** @var RouteCollection $collection */
        $collection = $this->getMock(RouteCollection::class);
        $this->router->setRouteCollection($collection);
        $this->assertSame($collection, $this->router->getRouteCollection());
    }

    /**
     * Tests specifying a group host
     */
    public function testSpecifyingGroupHost()
    {
        $this->router->group(["host" => "google.com"], function () {
            $this->router->get("/foo", "foo@bar");
            $this->router->post("/foo", "foo@bar");
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
        $this->router->group(["controllerNamespace" => "MyApp\\Controllers\\"], function () {
            $this->router->get("/foo", "ControllerA@myMethod");
            $this->router->post("/foo", "ControllerB@myMethod");
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
        $this->router->group(["controllerNamespace" => "MyApp\\Controllers"], function () {
            $this->router->get("/foo", "ControllerA@myMethod");
            $this->router->post("/foo", "ControllerB@myMethod");
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
        $this->router->group(["host" => "google.com"], function () {
            $this->router->group(["host" => "mail."], function () {
                $this->router->get("/foo", "foo@bar");
                $this->router->post("/foo", "foo@bar");
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
    ) {
        $controller = "$controllerName@$controllerMethod";
        $options = [
            "host" => $rawHost
        ];

        // The mock router will return the route used rather than the output of the route controller
        // This makes testing easier
        $mockRouter = new MockRouter();
        $deleteRoute = new Route(Request::METHOD_DELETE, $rawPath, $controller, $options);
        $getRoute = new Route(Request::METHOD_GET, $rawPath, $controller, $options);
        $postRoute = new Route(Request::METHOD_POST, $rawPath, $controller, $options);
        $putRoute = new Route(Request::METHOD_PUT, $rawPath, $controller, $options);
        $headRoute = new Route(Request::METHOD_HEAD, $rawPath, $controller, $options);
        $optionsRoute = new Route(Request::METHOD_OPTIONS, $rawPath, $controller, $options);
        $patchRoute = new Route(Request::METHOD_PATCH, $rawPath, $controller, $options);
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

        switch ($httpMethod) {
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

        $parsedRoute = $this->parser->parse($routeToHandle);
        $compiledRoute = $this->compiler->compile($parsedRoute, $request);
        $this->assertEquals($compiledRoute, $mockRouter->route($request));
        $this->assertEquals($compiledRoute, $mockRouter->getMatchedRoute());
        // The mock router does not actually instantiate the input controller
        // Instead, its dispatcher always sets the controller to the same object every time
        $this->assertInstanceOf("Opulence\\Routing\\Controller", $mockRouter->getMatchedController());
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
            MockController::class,
            "noParameters"
        );
        $this->doRoute(
            $httpMethod,
            "/foo/:foo",
            "/foo/123",
            ":bar.google.com",
            "mail.google.com",
            MockController::class,
            "twoParameters"
        );
        $this->doRoute(
            $httpMethod,
            "/foo/:foo/:bar",
            "/foo/123/456",
            ":baz.:blah.google.com",
            "u.mail.google.com",
            MockController::class,
            "severalParameters"
        );
    }
} 
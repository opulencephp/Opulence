<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Routing;

use Opulence\Http\HttpException;
use Opulence\Http\Requests\Request;
use Opulence\Http\Requests\RequestMethods;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Dispatchers\Dispatcher;
use Opulence\Routing\Routes\Compilers\Compiler;
use Opulence\Routing\Routes\Compilers\Matchers\HostMatcher;
use Opulence\Routing\Routes\Compilers\Matchers\PathMatcher;
use Opulence\Routing\Routes\Compilers\Matchers\SchemeMatcher;
use Opulence\Routing\Routes\Compilers\Parsers\Parser;
use Opulence\Routing\Routes\Route;
use Opulence\Routing\Routes\RouteCollection;
use Opulence\Ioc\Container;
use Opulence\Tests\Routing\Mocks\Controller as MockController;
use Opulence\Tests\Routing\Mocks\NonOpulenceController;
use Opulence\Tests\Routing\Mocks\Router as MockRouter;

/**
 * Tests the router
 */
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

        $this->router->group(["path" => "/foo/:id", "vars" => ["id" => "\d+"]], function () use (&$getRoute) {
            $getRoute = $this->router->get("/foo", "foo@bar");
        });
        $this->assertSame($getRoute, $this->router->getRouteCollection()->get(RequestMethods::GET)[0]);
    }

    /**
     * Tests a group with variable regexes
     */
    public function testGroupWithVariableRegexes()
    {
        $this->router->group(["path" => "/users/:userId", "vars" => ["id" => "\d+"]], function () {
            $this->router->get("/foo", "foo@bar");
            $this->router->post("/foo", "foo@bar");
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(RequestMethods::POST);
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
            $this->router->addRoute(new Route(RequestMethods::GET, "/bar", $controller));
            $this->router->delete("/blah", $controller);
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        /** @var Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRouteCollection()->get(RequestMethods::DELETE);
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
            $this->router->addRoute(new Route(RequestMethods::GET, "/bar", $controller, $routeOptions));
            $this->router->delete("/blah", $controller, $routeOptions);
        });
        $this->router->get("/asdf", $controller, $routeOptions);
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        /** @var Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRouteCollection()->get(RequestMethods::DELETE);
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
            $this->router->addRoute(new Route(RequestMethods::GET, "/bar", $controller, $routeOptions));
            $this->router->delete("/blah", $controller, $routeOptions);
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        /** @var Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRouteCollection()->get(RequestMethods::DELETE);
        $this->assertEquals(["foo1", "foo2"], $getRoutes[0]->getMiddleware());
        $this->assertEquals(["foo1", "foo2"], $deleteRoutes[0]->getMiddleware());
    }

    /**
     * Tests that the matched controller is null before routing
     */
    public function testMatchedControllerIsNullBeforeRouting()
    {
        $this->assertNull($this->router->getMatchedController());
    }

    /**
     * Tests mixing HTTPS on nested groups
     */
    public function testMixingHttpsOnNestedGroups()
    {
        $this->router->group(["https" => true], function () {
            $this->router->group(["https" => false], function () {
                $this->router->get("/foo", "foo@bar");
                $this->router->post("/foo", "foo@bar");
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
            $this->router->addRoute(new Route(RequestMethods::GET, "/bar", $outerRouteController));
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
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        /** @var Route[] $deleteRoutes */
        $deleteRoutes = $this->router->getRouteCollection()->get(RequestMethods::DELETE);
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
        $this->router->group(["path" => "/users/:userId", "vars" => ["id" => "\d*"]], function () {
            $this->router->get("/foo", "foo@bar");
            // This route's variable regex should take precedence
            $this->router->get("/bam", "foo@bam", ["vars" => ["id" => "\w+"]]);

            $this->router->group(["path" => "/bar", "vars" => ["id" => "\d+"]], function () {
                $this->router->get("/baz", "bar@baz");
            });
        });
        /** @var Route[] $getRoutes */
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
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
        $this->assertEquals(1, count($allRoutes[RequestMethods::GET]));
        $this->assertEquals(1, count($allRoutes[RequestMethods::POST]));
        $this->assertEquals(1, count($allRoutes[RequestMethods::DELETE]));
        $this->assertEquals(1, count($allRoutes[RequestMethods::PUT]));
    }

    /**
     * Tests routing a DELETE request
     */
    public function testRoutingDeleteRequest()
    {
        $this->doTestForHttpMethod(RequestMethods::DELETE);
    }

    /**
     * Tests routing a GET request
     */
    public function testRoutingGetRequest()
    {
        $this->doTestForHttpMethod(RequestMethods::GET);
    }

    /**
     * Tests routing a HEAD request
     */
    public function testRoutingHeadRequest()
    {
        $this->doTestForHttpMethod(RequestMethods::HEAD);
    }

    /**
     * Tests routing a missing path
     */
    public function testRoutingMissingPath()
    {
        $this->setExpectedException(HttpException::class);
        $request = new Request([], [], [], [
            "REQUEST_METHOD" => RequestMethods::GET,
            "REQUEST_URI" => "/foo/"
        ], [], []);
        $this->router->route($request);
    }

    /**
     * Tests routing for multiple methods
     */
    public function testRoutingMultipleMethods()
    {
        $controller = MockController::class . "@noParameters";
        $this->router->multiple([RequestMethods::GET, RequestMethods::POST], "/foo", $controller);
        $allRoutes = $this->router->getRouteCollection()->get();
        $this->assertEquals(1, count($allRoutes[RequestMethods::GET]));
        $this->assertEquals(1, count($allRoutes[RequestMethods::POST]));
        $this->assertEquals(0, count($allRoutes[RequestMethods::DELETE]));
        $this->assertEquals(0, count($allRoutes[RequestMethods::PUT]));
        $this->assertEquals(0, count($allRoutes[RequestMethods::HEAD]));
        $this->assertEquals(0, count($allRoutes[RequestMethods::OPTIONS]));
        $this->assertEquals(0, count($allRoutes[RequestMethods::PATCH]));
    }

    /**
     * Tests routing a OPTIONS request
     */
    public function testRoutingOptionsRequest()
    {
        $this->doTestForHttpMethod(RequestMethods::OPTIONS);
    }

    /**
     * Tests routing a PATCH request
     */
    public function testRoutingPatchRequest()
    {
        $this->doTestForHttpMethod(RequestMethods::PATCH);
    }

    /**
     * Tests routing a POST request
     */
    public function testRoutingPostRequest()
    {
        $this->doTestForHttpMethod(RequestMethods::POST);
    }

    /**
     * Tests routing a PUT request
     */
    public function testRoutingPutRequest()
    {
        $this->doTestForHttpMethod(RequestMethods::PUT);
    }

    /**
     * Tests routing to a non-Opulence controller
     */
    public function testRoutingToNonOpulenceController()
    {
        $controller = NonOpulenceController::class . "@index";
        $this->router->get("/foo/:id", $controller);
        $server = [
            "REQUEST_METHOD" => RequestMethods::GET,
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
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(RequestMethods::POST);
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
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(RequestMethods::POST);
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
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(RequestMethods::POST);
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
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(RequestMethods::POST);
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
        $getRoutes = $this->router->getRouteCollection()->get(RequestMethods::GET);
        /** @var Route[] $postRoutes */
        $postRoutes = $this->router->getRouteCollection()->get(RequestMethods::POST);
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
            "REQUEST_METHOD" => $httpMethod,
            "REQUEST_URI" => $pathToRoute,
            "HTTP_HOST" => $hostToRoute
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
    private function doTestForHttpMethod($httpMethod)
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
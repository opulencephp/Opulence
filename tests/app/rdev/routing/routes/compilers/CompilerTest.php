<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the route compiler
 */
namespace RDev\Routing\Routes\Compilers;
use RDev\HTTP\Requests\Request;
use RDev\Routing\Routes\Compilers\Matchers\HostMatcher;
use RDev\Routing\Routes\Compilers\Matchers\PathMatcher;
use RDev\Routing\Routes\Compilers\Matchers\SchemeMatcher;
use RDev\Routing\Routes\ParsedRoute;

class CompilerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Compiler The compiler to use in tests */
    private $compiler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $routeMatchers = [
            new SchemeMatcher(),
            new HostMatcher(),
            new PathMatcher()
        ];
        $this->compiler = new Compiler($routeMatchers);
    }

    /**
     * Tests compiling an insecure route over HTTPS
     */
    public function testCompilingInsecureRouteOnHTTPS()
    {
        $route = $this->getParsedRoute(Request::METHOD_GET, "foo@bar", false, ".*", '\/');
        $request = new Request([], [], [], [
            "REQUEST_METHOD" => Request::METHOD_GET,
            "REQUEST_URI" => "/",
            "HTTPS" => true
        ], [], []);
        $compiledRoute = $this->compiler->compile($route, $request);
        $this->assertTrue($compiledRoute->isMatch());
        $this->assertEquals([], $compiledRoute->getPathVariables());
    }

    /**
     * Tests compiling a route with path variables
     */
    public function testCompilingRouteWitPathVariables()
    {
        $route = $this->getParsedRoute(Request::METHOD_GET, "foo@bar", false, ".*", '\/foo\/(?P<bar>[^\/]+)\/(?P<baz>[^\/]+)');
        $request = new Request([], [], [], [
            "REQUEST_METHOD" => Request::METHOD_GET,
            "REQUEST_URI" => "/foo/12/34"
        ], [], []);
        $compiledRoute = $this->compiler->compile($route, $request);
        $this->assertTrue($compiledRoute->isMatch());
        $expectedPathVariables = [
            "bar" => "12",
            "baz" => "34",
            0 => "12",
            1 => "34"
        ];
        $this->assertEquals($expectedPathVariables, $compiledRoute->getPathVariables());
    }

    /**
     * Tests compiling a route with an optional variable
     */
    public function testCompilingRouteWithOptionalVariable()
    {
        $route = $this->getParsedRoute(Request::METHOD_GET, "foo@bar", false, ".*", '\/foo\/(?P<bar>[^\/]+)?');
        $request = new Request([], [], [], [
            "REQUEST_METHOD" => Request::METHOD_GET,
            "REQUEST_URI" => "/foo/"
        ], [], []);
        $compiledRoute = $this->compiler->compile($route, $request);
        $this->assertTrue($compiledRoute->isMatch());
        $this->assertEquals([], $compiledRoute->getPathVariables());
    }

    /**
     * Tests compiling a route with an optional variable with a default value
     */
    public function testCompilingRouteWithOptionalVariableWithDefaultValue()
    {
        $route = $this->getParsedRoute(Request::METHOD_GET, "foo@bar", false, ".*", '\/bar\/(?P<foo>[^\/]+)?');
        $request = new Request([], [], [], [
            "REQUEST_METHOD" => Request::METHOD_GET,
            "REQUEST_URI" => "/bar/"
        ], [], []);
        $compiledRoute = $this->compiler->compile($route, $request);
        $this->assertTrue($compiledRoute->isMatch());
        $this->assertEquals([], $compiledRoute->getPathVariables());
    }

    /**
     * Tests getting the route variables for an unmatched route
     */
    public function testGettingRouteVariablesForUnmatchedRoute()
    {
        $route = $this->getParsedRoute(Request::METHOD_GET, "foo@bar", false, ".*", '\/foo');
        $request = new Request([], [], [], [
            "REQUEST_METHOD" => Request::METHOD_GET,
            "REQUEST_URI" => "/bar"
        ], [], []);
        $compiledRoute = $this->compiler->compile($route, $request);
        $this->assertEquals([], $compiledRoute->getPathVariables());
    }

    /**
     * Tests matching a secure route
     */
    public function testMatchingSecureRoute()
    {
        $route = $this->getParsedRoute(Request::METHOD_GET, "foo@bar", true, ".*", '\/');
        $request = new Request([], [], [], [
            "REQUEST_METHOD" => Request::METHOD_GET,
            "REQUEST_URI" => "/",
            "HTTPS" => true
        ], [], []);
        $compiledRoute = $this->compiler->compile($route, $request);
        $this->assertTrue($compiledRoute->isMatch());
        $this->assertEquals([], $compiledRoute->getPathVariables());
    }

    /**
     * Tests trying to match a secure route when not running on HTTPS
     */
    public function testNotBeingHTTPSAndMatchingSecureRoute()
    {
        $route = $this->getParsedRoute(Request::METHOD_GET, "foo@bar", true, ".*", '\/');
        $request = new Request([], [], [], [
            "REQUEST_METHOD" => Request::METHOD_GET,
            "REQUEST_URI" => "/"
        ], [], []);
        $compiledRoute = $this->compiler->compile($route, $request);
        $this->assertFalse($compiledRoute->isMatch());
    }

    /**
     * Gets a route for testing
     *
     * @param string $method The HTTP method
     * @param string|callable $controller The controller
     * @param bool $isSecure Whether or not the route is secure
     * @param string $hostRegex The host regex
     * @param string $pathRegex The path regex
     * @return ParsedRoute|\PHPUnit_Framework_MockObject_MockObject The parsed route
     */
    private function getParsedRoute($method, $controller, $isSecure, $hostRegex, $pathRegex)
    {
        $route = $this->getMock(ParsedRoute::class, [], [], "", false);
        $route->expects($this->any())->method("getMethod")->willReturn($method);
        $route->expects($this->any())->method("isSecure")->willReturn($isSecure);
        $route->expects($this->any())->method("getController")->willReturn($controller);
        $route->expects($this->any())->method("getHostRegex")->willReturn("#^$hostRegex$#");
        $route->expects($this->any())->method("getPathRegex")->willReturn("#^$pathRegex$#");

        return $route;
    }
}
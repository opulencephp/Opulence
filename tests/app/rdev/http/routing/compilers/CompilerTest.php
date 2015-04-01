<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the route compiler
 */
namespace RDev\HTTP\Routing\Compilers;
use RDev\HTTP\Requests\Request;
use RDev\HTTP\Routing\Compilers\Parsers\Parser;
use RDev\HTTP\Routing\Routes\Route;

class CompilerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Compiler The compiler to use in tests */
    private $compiler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->compiler = new Compiler(new Parser());
    }

    /**
     * Tests compiling an insecure route over HTTPS
     */
    public function testCompilingInsecureRouteOnHTTPS()
    {
        $route = new Route("GET", "/", ["controller" => "foo@bar"]);
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
        $route = new Route("GET", "/foo/{bar}/{baz}", ["controller" => "foo@bar"]);
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
        $route = new Route("GET", "/foo/{bar?}", ["controller" => "foo@bar"]);
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
        $route = new Route("GET", "/bar/{foo?=23}", ["controller" => "foo@bar"]);
        $request = new Request([], [], [], [
            "REQUEST_METHOD" => Request::METHOD_GET,
            "REQUEST_URI" => "/bar/"
        ], [], []);
        $compiledRoute = $this->compiler->compile($route, $request);
        $this->assertTrue($compiledRoute->isMatch());
        $this->assertEquals("23", $compiledRoute->getDefaultValue("foo"));
        $this->assertEquals([], $compiledRoute->getPathVariables());
    }

    /**
     * Tests getting the route variables for an unmatched route
     */
    public function testGettingRouteVariablesForUnmatchedRoute()
    {
        $route = new Route("GET", "/foo", ["controller" => "foo@bar"]);
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
        $route = new Route("GET", "/", ["controller" => "foo@bar", "https" => true]);
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
        $route = new Route("GET", "/", ["controller" => "foo@bar", "https" => true]);
        $request = new Request([], [], [], [
            "REQUEST_METHOD" => Request::METHOD_GET,
            "REQUEST_URI" => "/"
        ], [], []);
        $compiledRoute = $this->compiler->compile($route, $request);
        $this->assertFalse($compiledRoute->isMatch());
    }
}
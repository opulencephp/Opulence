<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Routing\Tests\Routes\Compilers;

use Opulence\Http\Requests\Request;
use Opulence\Http\Requests\RequestMethods;
use Opulence\Routing\Routes\Compilers\Compiler;
use Opulence\Routing\Routes\Compilers\Matchers\HostMatcher;
use Opulence\Routing\Routes\Compilers\Matchers\PathMatcher;
use Opulence\Routing\Routes\Compilers\Matchers\SchemeMatcher;
use Opulence\Routing\Routes\ParsedRoute;

/**
 * Tests the route compiler
 */
class CompilerTest extends \PHPUnit\Framework\TestCase
{
    /** @var Compiler The compiler to use in tests */
    private $compiler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $routeMatchers = [
            new PathMatcher(),
            new HostMatcher(),
            new SchemeMatcher()
        ];
        $this->compiler = new Compiler($routeMatchers);
    }

    /**
     * Tests compiling an insecure route over HTTPS
     */
    public function testCompilingInsecureRouteOnHttps()
    {
        $route = $this->getParsedRoute(RequestMethods::GET, 'foo@bar', false, '.*', '\/');
        $request = new Request([], [], [], [
            'REQUEST_METHOD' => RequestMethods::GET,
            'REQUEST_URI' => '/',
            'HTTPS' => true
        ], [], []);
        $compiledRoute = $this->compiler->compile($route, $request);
        $this->assertTrue($compiledRoute->isMatch());
        $this->assertEquals([], $compiledRoute->getPathVars());
    }

    /**
     * Tests compiling a route with path variables
     */
    public function testCompilingRouteWitPathVariables()
    {
        $route = $this->getParsedRoute(RequestMethods::GET, 'foo@bar', false, '.*',
            '\/foo\/(?P<bar>[^\/]+)\/(?P<baz>[^\/]+)');
        $request = new Request([], [], [], [
            'REQUEST_METHOD' => RequestMethods::GET,
            'REQUEST_URI' => '/foo/12/34'
        ], [], []);
        $compiledRoute = $this->compiler->compile($route, $request);
        $this->assertTrue($compiledRoute->isMatch());
        $expectedPathVariables = [
            'bar' => '12',
            'baz' => '34',
            0 => '12',
            1 => '34'
        ];
        $this->assertEquals($expectedPathVariables, $compiledRoute->getPathVars());
    }

    /**
     * Tests compiling a route with an optional variable
     */
    public function testCompilingRouteWithOptionalVariable()
    {
        $route = $this->getParsedRoute(RequestMethods::GET, 'foo@bar', false, '.*', '\/foo\/(?P<bar>[^\/]+)?');
        $request = new Request([], [], [], [
            'REQUEST_METHOD' => RequestMethods::GET,
            'REQUEST_URI' => '/foo/'
        ], [], []);
        $compiledRoute = $this->compiler->compile($route, $request);
        $this->assertTrue($compiledRoute->isMatch());
        $this->assertEquals([], $compiledRoute->getPathVars());
    }

    /**
     * Tests compiling a route with an optional variable with a default value
     */
    public function testCompilingRouteWithOptionalVariableWithDefaultValue()
    {
        $route = $this->getParsedRoute(RequestMethods::GET, 'foo@bar', false, '.*', '\/bar\/(?P<foo>[^\/]+)?');
        $request = new Request([], [], [], [
            'REQUEST_METHOD' => RequestMethods::GET,
            'REQUEST_URI' => '/bar/'
        ], [], []);
        $compiledRoute = $this->compiler->compile($route, $request);
        $this->assertTrue($compiledRoute->isMatch());
        $this->assertEquals([], $compiledRoute->getPathVars());
    }

    /**
     * Tests getting the route variables for an unmatched route
     */
    public function testGettingRouteVariablesForUnmatchedRoute()
    {
        $route = $this->getParsedRoute(RequestMethods::GET, 'foo@bar', false, '.*', '\/foo');
        $request = new Request([], [], [], [
            'REQUEST_METHOD' => RequestMethods::GET,
            'REQUEST_URI' => '/bar'
        ], [], []);
        $compiledRoute = $this->compiler->compile($route, $request);
        $this->assertEquals([], $compiledRoute->getPathVars());
    }

    /**
     * Tests matching a secure route
     */
    public function testMatchingSecureRoute()
    {
        $route = $this->getParsedRoute(RequestMethods::GET, 'foo@bar', true, '.*', '\/');
        $request = new Request([], [], [], [
            'REQUEST_METHOD' => RequestMethods::GET,
            'REQUEST_URI' => '/',
            'HTTPS' => true
        ], [], []);
        $compiledRoute = $this->compiler->compile($route, $request);
        $this->assertTrue($compiledRoute->isMatch());
        $this->assertEquals([], $compiledRoute->getPathVars());
    }

    /**
     * Tests trying to match a secure route when not running on HTTPS
     */
    public function testNotBeingHttpsAndMatchingSecureRoute()
    {
        $route = $this->getParsedRoute(RequestMethods::GET, 'foo@bar', true, '.*', '\/');
        $request = new Request([], [], [], [
            'REQUEST_METHOD' => RequestMethods::GET,
            'REQUEST_URI' => '/'
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
        $route = $this->getMockBuilder(ParsedRoute::class)
            ->disableOriginalConstructor()
            ->getMock();
        $route->expects($this->any())->method('getMethods')->willReturn([$method]);
        $route->expects($this->any())->method('isSecure')->willReturn($isSecure);
        $route->expects($this->any())->method('getController')->willReturn($controller);
        $route->expects($this->any())->method('getHostRegex')->willReturn("#^$hostRegex$#");
        $route->expects($this->any())->method('getPathRegex')->willReturn("#^$pathRegex$#");
        // The following data are necessary in the parsed route's constructor
        $route->expects($this->any())->method('getRawPath')->willReturn('');
        $route->expects($this->any())->method('getRawHost')->willReturn('');
        $route->expects($this->any())->method('getName')->willReturn('');

        return $route;
    }
}

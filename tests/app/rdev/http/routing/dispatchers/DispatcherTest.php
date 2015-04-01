<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the dispatcher class
 */
namespace RDev\HTTP\Routing\Dispatchers;
use RDev\HTTP\Requests\Request;
use RDev\HTTP\Responses\RedirectResponse;
use RDev\HTTP\Responses\Response;
use RDev\HTTP\Routing\Routes\CompiledRoute;
use RDev\HTTP\Routing\Routes\ParsedRoute;
use RDev\HTTP\Routing\Routes\Route;
use RDev\IoC\Container;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @var Dispatcher The dispatcher to use in tests */
    private $dispatcher = null;
    /** @var Request The request to use in tests */
    private $request = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->dispatcher = new Dispatcher(new Container());
        $this->request = new Request([], [], [], [], [], []);
    }

    /**
     * Tests calling a controller that does not extend the base controller
     */
    public function testCallingInvalidController()
    {
        $this->setExpectedException("RDev\\HTTP\\Routing\\RouteException");
        $route = $this->getCompiledRoute(
            new Route(["GET"], "/foo", ["controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\InvalidController@foo"])
        );
        $this->dispatcher->dispatch($route, $this->request);
    }

    /**
     * Tests calling a non-existent controller
     */
    public function testCallingNonExistentController()
    {
        $this->setExpectedException("RDev\\HTTP\\Routing\\RouteException");
        $route = $this->getCompiledRoute(
            new Route(["GET"], "/foo", ["controller" => "RDev\\Controller\\That\\Does\\Not\\Exist@foo"])
        );
        $this->dispatcher->dispatch($route, $this->request);
    }

    /**
     * Tests calling a method that does not exists in a controller
     */
    public function testCallingNonExistentMethod()
    {
        $this->setExpectedException("RDev\\HTTP\\Routing\\RouteException");
        $route = $this->getCompiledRoute(
            new Route(["GET"], "/foo", ["controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@doesNotExist"])
        );
        $this->dispatcher->dispatch($route, $this->request);
    }

    /**
     * Tests calling a private method in a controller
     */
    public function testCallingPrivateMethod()
    {
        $this->setExpectedException("RDev\\HTTP\\Routing\\RouteException");
        $route = $this->getCompiledRoute(
            new Route(["GET"], "/foo", ["controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@privateMethod"])
        );
        $this->dispatcher->dispatch($route, $this->request);
    }

    /**
     * Tests calling a protected method in a controller
     */
    public function testCallingProtectedMethod()
    {
        $route = $this->getCompiledRoute(
            new Route(["GET"], "/foo", ["controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@protectedMethod"])
        );
        $this->assertEquals("protectedMethod", $this->dispatcher->dispatch($route, $this->request)->getContent());
    }

    /**
     * Tests chaining middleware that do and do not return something
     */
    public function testChainingMiddlewareThatDoAndDoNotReturnSomething()
    {
        $options = [
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@noParameters",
            "middleware" => [
                "RDev\\Tests\\HTTP\\Routing\\Mocks\\ReturnsSomethingMiddleware",
                "RDev\\Tests\\HTTP\\Routing\\Mocks\\DoesNotReturnSomethingMiddleware"
            ]
        ];
        $route = $this->getCompiledRoute(new Route(["GET"], "/foo", $options));
        $this->assertEquals(
            "noParameters:something",
            $this->dispatcher->dispatch($route, $this->request)->getContent()
        );
    }

    /**
     * Tests specifying an invalid middleware
     */
    public function testInvalidMiddleware()
    {
        $this->setExpectedException("RDev\\HTTP\\Routing\\RouteException");
        $options = [
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@returnsNothing",
            "middleware" => get_class($this)
        ];
        $route = $this->getCompiledRoute(new Route(["GET"], "/foo", $options));
        $this->dispatcher->dispatch($route, $this->request);
    }

    /**
     * Tests using a middleware that returns something with a controller that also returns something
     */
    public function testMiddlewareThatAddsToControllerResponse()
    {
        $options = [
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@noParameters",
            "middleware" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\ReturnsSomethingMiddleware"
        ];
        $route = $this->getCompiledRoute(new Route(["GET"], "/foo", $options));
        $this->assertEquals(
            "noParameters:something",
            $this->dispatcher->dispatch($route, $this->request)->getContent()
        );
    }

    /**
     * Tests that controller is set
     */
    public function testThatControllerIsSet()
    {
        $controller = null;
        $expectedControllerClass = "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller";
        $options = ["controller" => "$expectedControllerClass@returnsNothing"];
        $route = $this->getCompiledRoute(new Route(["GET"], "/foo", $options));
        $this->assertEquals(new Response(), $this->dispatcher->dispatch($route, $this->request, $controller));
        $this->assertInstanceOf($expectedControllerClass, $controller);
    }

    /**
     * Tests using a middleware that does not return anything
     */
    public function testUsingMiddlewareThatDoesNotReturnAnything()
    {
        $options = [
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@returnsNothing",
            "middleware" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\DoesNotReturnSomethingMiddleware"
        ];
        $route = $this->getCompiledRoute(new Route(["GET"], "/foo", $options));
        $this->assertEquals(new Response(), $this->dispatcher->dispatch($route, $this->request));
    }

    /**
     * Tests using a middleware that returns something
     */
    public function testUsingMiddlewareThatReturnsSomething()
    {
        $options = [
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@returnsNothing",
            "middleware" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\ReturnsSomethingMiddleware"
        ];
        $route = $this->getCompiledRoute(new Route(["GET"], "/foo", $options));
        $this->assertEquals(new RedirectResponse("/bar"), $this->dispatcher->dispatch($route, $this->request));
    }

    /**
     * Gets the compiled route
     *
     * @param Route $route The route to compile
     * @return CompiledRoute The compiled route
     */
    private function getCompiledRoute(Route $route)
    {
        $parsedRoute = new ParsedRoute($route);

        return new CompiledRoute($parsedRoute, false);
    }
} 
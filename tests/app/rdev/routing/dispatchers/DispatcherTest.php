<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the dispatcher class
 */
namespace RDev\Routing\Dispatchers;
use RDev\HTTP\Requests\Request;
use RDev\HTTP\Responses\RedirectResponse;
use RDev\HTTP\Responses\Response;
use RDev\IoC\Container;
use RDev\Routing\Routes\CompiledRoute;
use RDev\Routing\Routes\ParsedRoute;
use RDev\Routing\Routes\Route;
use RDev\Tests\Routing\Mocks\NonRDevController;

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
     * Tests calling a non-existent controller
     */
    public function testCallingNonExistentController()
    {
        $this->setExpectedException("RDev\\Routing\\RouteException");
        $route = $this->getCompiledRoute(
            new Route(["GET"], "/foo", "RDev\\Controller\\That\\Does\\Not\\Exist@foo")
        );
        $this->dispatcher->dispatch($route, $this->request);
    }

    /**
     * Tests calling a method that does not exists in a controller
     */
    public function testCallingNonExistentMethod()
    {
        $this->setExpectedException("RDev\\Routing\\RouteException");
        $route = $this->getCompiledRoute(
            new Route(["GET"], "/foo", "RDev\\Tests\\Routing\\Mocks\\Controller@doesNotExist")
        );
        $this->dispatcher->dispatch($route, $this->request);
    }

    /**
     * Tests calling a non-RDev controller
     */
    public function testCallingNonRDevController()
    {
        $route = $this->getCompiledRoute(
            new Route(["GET"], "/foo/123", NonRDevController::class . "@index")
        );
        $route->setPathVariables(["id" => "123"]);
        $controller = null;
        $response = $this->dispatcher->dispatch($route, $this->request, $controller);
        $this->assertInstanceOf(NonRDevController::class, $controller);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals("Id: 123", $response->getContent());
    }

    /**
     * Tests calling a private method in a controller
     */
    public function testCallingPrivateMethod()
    {
        $this->setExpectedException("RDev\\Routing\\RouteException");
        $route = $this->getCompiledRoute(
            new Route(["GET"], "/foo", "RDev\\Tests\\Routing\\Mocks\\Controller@privateMethod")
        );
        $this->dispatcher->dispatch($route, $this->request);
    }

    /**
     * Tests calling a protected method in a controller
     */
    public function testCallingProtectedMethod()
    {
        $route = $this->getCompiledRoute(
            new Route(["GET"], "/foo", "RDev\\Tests\\Routing\\Mocks\\Controller@protectedMethod")
        );
        $this->assertEquals("protectedMethod", $this->dispatcher->dispatch($route, $this->request)->getContent());
    }

    /**
     * Tests chaining middleware that do and do not return something
     */
    public function testChainingMiddlewareThatDoAndDoNotReturnSomething()
    {
        $controller = "RDev\\Tests\\Routing\\Mocks\\Controller@noParameters";
        $options = [
            "middleware" => [
                "RDev\\Tests\\Routing\\Mocks\\ReturnsSomethingMiddleware",
                "RDev\\Tests\\Routing\\Mocks\\DoesNotReturnSomethingMiddleware"
            ]
        ];
        $route = $this->getCompiledRoute(new Route(["GET"], "/foo", $controller, $options));
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
        $this->setExpectedException("RDev\\Routing\\RouteException");
        $controller = "RDev\\Tests\\Routing\\Mocks\\Controller@returnsNothing";
        $options = [
            "middleware" => get_class($this)
        ];
        $route = $this->getCompiledRoute(new Route(["GET"], "/foo", $controller, $options));
        $this->dispatcher->dispatch($route, $this->request);
    }

    /**
     * Tests using a middleware that returns something with a controller that also returns something
     */
    public function testMiddlewareThatAddsToControllerResponse()
    {
        $controller = "RDev\\Tests\\Routing\\Mocks\\Controller@noParameters";
        $options = [
            "middleware" => "RDev\\Tests\\Routing\\Mocks\\ReturnsSomethingMiddleware"
        ];
        $route = $this->getCompiledRoute(new Route(["GET"], "/foo", $controller, $options));
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
        $expectedControllerClass = "RDev\\Tests\\Routing\\Mocks\\Controller";
        $route = $this->getCompiledRoute(new Route(["GET"], "/foo", "$expectedControllerClass@returnsNothing"));
        $this->assertEquals(new Response(), $this->dispatcher->dispatch($route, $this->request, $controller));
        $this->assertInstanceOf($expectedControllerClass, $controller);
    }

    /**
     * Tests using a middleware that does not return anything
     */
    public function testUsingMiddlewareThatDoesNotReturnAnything()
    {
        $controller = "RDev\\Tests\\Routing\\Mocks\\Controller@returnsNothing";
        $options = [
            "middleware" => "RDev\\Tests\\Routing\\Mocks\\DoesNotReturnSomethingMiddleware"
        ];
        $route = $this->getCompiledRoute(new Route(["GET"], "/foo", $controller, $options));
        $this->assertEquals(new Response(), $this->dispatcher->dispatch($route, $this->request));
    }

    /**
     * Tests using a middleware that returns something
     */
    public function testUsingMiddlewareThatReturnsSomething()
    {
        $controller = "RDev\\Tests\\Routing\\Mocks\\Controller@returnsNothing";
        $options = [
            "middleware" => "RDev\\Tests\\Routing\\Mocks\\ReturnsSomethingMiddleware"
        ];
        $route = $this->getCompiledRoute(new Route(["GET"], "/foo", $controller, $options));
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
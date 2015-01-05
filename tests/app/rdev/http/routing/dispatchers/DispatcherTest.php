<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the dispatcher class
 */
namespace RDev\HTTP\Routing\Dispatchers;
use RDev\HTTP\Requests;
use RDev\HTTP\Responses;
use RDev\HTTP\Routing\Routes;
use RDev\IoC;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @var Dispatcher The dispatcher to use in tests */
    private $dispatcher = null;
    /** @var Requests\Request The request to use in tests */
    private $request = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->dispatcher = new Dispatcher(new IoC\Container());
        $this->request = new Requests\Request([], [], [], [], [], []);
    }

    /**
     * Tests calling a controller that does not extend the base controller
     */
    public function testCallingInvalidController()
    {
        $this->setExpectedException("RDev\\HTTP\\Routing\\RouteException");
        $route = $this->getCompiledRoute(
            new Routes\Route(["GET"], "/foo", ["controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\InvalidController@foo"])
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
            new Routes\Route(["GET"], "/foo", ["controller" => "RDev\\Controller\\That\\Does\\Not\\Exist@foo"])
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
            new Routes\Route(["GET"], "/foo", ["controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@doesNotExist"])
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
            new Routes\Route(["GET"], "/foo", ["controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@privateMethod"])
        );
        $this->dispatcher->dispatch($route, $this->request);
    }

    /**
     * Tests calling a protected method in a controller
     */
    public function testCallingProtectedMethod()
    {
        $route = $this->getCompiledRoute(
            new Routes\Route(["GET"], "/foo", ["controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@protectedMethod"])
        );
        $this->assertEquals("protectedMethod", $this->dispatcher->dispatch($route, $this->request)->getContent());
    }

    /**
     * Tests chaining post-filters that do and do not return something
     */
    public function testChainingPostFiltersThatDoAndDoNotReturnSomething()
    {
        $options = [
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@noParameters",
            "post" => [
                "RDev\\Tests\\HTTP\\Routing\\Mocks\\ReturnsSomethingFilter",
                "RDev\\Tests\\HTTP\\Routing\\Mocks\\DoesNotReturnSomethingFilter"
            ]
        ];
        $route = $this->getCompiledRoute(new Routes\Route(["GET"], "/foo", $options));
        $this->assertEquals(
            "noParameters:something",
            $this->dispatcher->dispatch($route, $this->request)->getContent()
        );
    }

    /**
     * Tests specifying an invalid filter
     */
    public function testInvalidFilter()
    {
        $this->setExpectedException("RDev\\HTTP\\Routing\\RouteException");
        $options = [
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@returnsNothing",
            "post" => get_class($this)
        ];
        $route = $this->getCompiledRoute(new Routes\Route(["GET"], "/foo", $options));
        $this->dispatcher->dispatch($route, $this->request);
    }

    /**
     * Tests using a post-filter that returns something with a controller that also returns something
     */
    public function testPostFilterThatAddsToControllerResponse()
    {
        $options = [
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@noParameters",
            "post" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\ReturnsSomethingFilter"
        ];
        $route = $this->getCompiledRoute(new Routes\Route(["GET"], "/foo", $options));
        $this->assertEquals(
            "noParameters:something",
            $this->dispatcher->dispatch($route, $this->request)->getContent()
        );
    }

    /**
     * Tests using a post-filter that does not return anything
     */
    public function testUsingPostFilterThatDoesNotReturnAnything()
    {
        $options = [
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@returnsNothing",
            "post" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\DoesNotReturnSomethingFilter"
        ];
        $route = $this->getCompiledRoute(new Routes\Route(["GET"], "/foo", $options));
        $this->assertEquals(new Responses\Response(), $this->dispatcher->dispatch($route, $this->request));
    }

    /**
     * Tests using a post-filter that returns something
     */
    public function testUsingPostFilterThatReturnsSomething()
    {
        $options = [
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@returnsNothing",
            "post" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\ReturnsSomethingFilter"
        ];
        $route = $this->getCompiledRoute(new Routes\Route(["GET"], "/foo", $options));
        $this->assertEquals(new Responses\RedirectResponse("/bar"), $this->dispatcher->dispatch($route, $this->request));
    }

    /**
     * Tests using a pre-filter that does not return anything
     */
    public function testUsingPreFilterThatDoesNotReturnAnything()
    {
        $options = [
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@noParameters",
            "pre" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\DoesNotReturnSomethingFilter"
        ];
        $route = $this->getCompiledRoute(new Routes\Route(["GET"], "/foo", $options));
        $this->assertEquals("noParameters", $this->dispatcher->dispatch($route, $this->request)->getContent());
    }

    /**
     * Tests using a pre-filter that returns something
     */
    public function testUsingPreFilterThatReturnsSomething()
    {
        $options = [
            "controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@noParameters",
            "pre" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\ReturnsSomethingFilter"
        ];
        $route = $this->getCompiledRoute(new Routes\Route(["GET"], "/foo", $options));
        $this->assertEquals(new Responses\RedirectResponse("/bar"), $this->dispatcher->dispatch($route, $this->request));
    }

    /**
     * Gets the compiled route
     *
     * @param Routes\Route $route The route to compile
     * @return Routes\CompiledRoute The compiled route
     */
    private function getCompiledRoute(Routes\Route $route)
    {
        $parsedRoute = new Routes\ParsedRoute($route);

        return new Routes\CompiledRoute($parsedRoute, false);
    }
} 
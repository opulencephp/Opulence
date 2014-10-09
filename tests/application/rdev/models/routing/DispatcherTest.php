<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the dispatcher class
 */
namespace RDev\Models\Routing;
use RDev\Models\HTTP;
use RDev\Models\IoC;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @var Dispatcher The dispatcher to use in tests */
    private $dispatcher = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->dispatcher = new Dispatcher(new IoC\Container());
    }

    /**
     * Tests calling a controller that does not extend the base controller
     */
    public function testCallingInvalidController()
    {
        $this->setExpectedException("RDev\\Models\\Routing\\RouteException");
        $route = new Route(["GET"], "/foo", ["controller" => "RDev\\Tests\\Controllers\\Mocks\\InvalidController@foo"]);
        $this->dispatcher->dispatch($route, []);
    }

    /**
     * Tests calling a non-existent controller
     */
    public function testCallingNonExistentController()
    {
        $this->setExpectedException("RDev\\Models\\Routing\\RouteException");
        $route = new Route(["GET"], "/foo", ["controller" => "RDev\\Controller\\That\\Does\\Not\\Exist@foo"]);
        $this->dispatcher->dispatch($route, []);
    }

    /**
     * Tests calling a method that does not exists in a controller
     */
    public function testCallingNonExistentMethod()
    {
        $this->setExpectedException("RDev\\Models\\Routing\\RouteException");
        $route = new Route(["GET"], "/foo", ["controller" => "RDev\\Tests\\Controllers\\Mocks\\Controller@doesNotExist"]);
        $this->dispatcher->dispatch($route, []);
    }

    /**
     * Tests calling a private method in a controller
     */
    public function testCallingPrivateMethod()
    {
        $this->setExpectedException("RDev\\Models\\Routing\\RouteException");
        $route = new Route(["GET"], "/foo", ["controller" => "RDev\\Tests\\Controllers\\Mocks\\Controller@privateMethod"]);
        $this->dispatcher->dispatch($route, []);
    }

    /**
     * Tests calling a protected method in a controller
     */
    public function testCallingProtectedMethod()
    {
        $route = new Route(["GET"], "/foo", ["controller" => "RDev\\Tests\\Controllers\\Mocks\\Controller@protectedMethod"]);
        $this->assertEquals("protectedMethod", $this->dispatcher->dispatch($route, []));
    }

    /**
     * Tests using a post-filter that does not return anything
     */
    public function testUsingPostFilterThatDoesNotReturnAnything()
    {
        $options = [
            "controller" => "RDev\\Tests\\Controllers\\Mocks\\Controller@returnsNothing",
            "post" => "foo"
        ];
        $route = new Route(["GET"], "/foo", $options);
        $this->dispatcher->registerFilter("foo", function ()
        {
            count($_SERVER);
        });
        $this->assertEquals(new HTTP\Response(), $this->dispatcher->dispatch($route, []));
    }

    /**
     * Tests using a post-filter that returns something
     */
    public function testUsingPostFilterThatReturnsSomething()
    {
        $options = [
            "controller" => "RDev\\Tests\\Controllers\\Mocks\\Controller@returnsNothing",
            "post" => "foo"
        ];
        $route = new Route(["GET"], "/foo", $options);
        $this->dispatcher->registerFilter("foo", function ()
        {
            return "YAY";
        });
        $this->assertEquals("YAY", $this->dispatcher->dispatch($route, []));
    }

    /**
     * Tests using a pre-filter that does not return anything
     */
    public function testUsingPreFilterThatDoesNotReturnAnything()
    {
        $options = [
            "controller" => "RDev\\Tests\\Controllers\\Mocks\\Controller@noParameters",
            "pre" => "foo"
        ];
        $route = new Route(["GET"], "/foo", $options);
        $this->dispatcher->registerFilter("foo", function ()
        {
            count($_SERVER);
        });
        $this->assertEquals("noParameters", $this->dispatcher->dispatch($route, []));
    }

    /**
     * Tests using a pre-filter that returns something
     */
    public function testUsingPreFilterThatReturnsSomething()
    {
        $options = [
            "controller" => "RDev\\Tests\\Controllers\\Mocks\\Controller@noParameters",
            "pre" => "foo"
        ];
        $route = new Route(["GET"], "/foo", $options);
        $this->dispatcher->registerFilter("foo", function ()
        {
            return "YAY";
        });
        $this->assertEquals("YAY", $this->dispatcher->dispatch($route, []));
    }

    /**
     * Tests using an unregistered post-filter
     */
    public function testUsingUnregisteredPostFilter()
    {
        $this->setExpectedException("RDev\\Models\\Routing\\RouteException");
        $options = [
            "controller" => "RDev\\Tests\\Controllers\\Mocks\\Controller@returnsNothing",
            "post" => "fakeFilter"
        ];
        $route = new Route(["GET"], "/foo", $options);
        $this->dispatcher->dispatch($route, []);
    }

    /**
     * Tests using an unregistered pre-filter
     */
    public function testUsingUnregisteredPreFilter()
    {
        $this->setExpectedException("RDev\\Models\\Routing\\RouteException");
        $options = [
            "controller" => "RDev\\Tests\\Controllers\\Mocks\\Controller@returnsNothing",
            "pre" => "fakeFilter"
        ];
        $route = new Route(["GET"], "/foo", $options);
        $this->dispatcher->dispatch($route, []);
    }
} 
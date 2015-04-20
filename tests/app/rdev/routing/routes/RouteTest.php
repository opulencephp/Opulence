<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the route class
 */
namespace RDev\Routing\Routes;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding multiple middleware
     */
    public function testAddingMultipleMiddleware()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->addMiddleware(["foo", "bar"]);
        $this->assertEquals(["foo", "bar"], $route->getMiddleware());
    }

    /**
     * Tests adding non-unique middleware
     */
    public function testAddingNonUniqueMiddleware()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->addMiddleware("foo");
        $route->addMiddleware("foo");
        $this->assertEquals(["foo"], $route->getMiddleware());
    }

    /**
     * Tests adding a single middleware
     */
    public function testAddingSingleMiddleware()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->addMiddleware("foo");
        $this->assertEquals(["foo"], $route->getMiddleware());
    }

    /**
     * Tests getting the controller method
     */
    public function testGettingControllerMethod()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $this->assertEquals("bar", $route->getControllerMethod());
    }

    /**
     * Tests getting the controller name
     */
    public function testGettingControllerName()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $this->assertEquals("foo", $route->getControllerName());
    }

    /**
     * Tests getting the methods
     */
    public function testGettingMethods()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/foo", $options);
        $this->assertEquals(["get"], $route->getMethods());
    }

    /**
     * Tests getting the middleware when it is a string
     */
    public function testGettingMiddlewareWhenItIsAString()
    {
        $options = [
            "controller" => "foo@bar",
            "middleware" => "foo"
        ];
        $route = new Route("get", "/foo", $options);
        $this->assertEquals(["foo"], $route->getMiddleware());
    }

    /**
     * Tests getting the middleware when they are an array
     */
    public function testGettingMiddlewareWhenTheyAreAnArray()
    {
        $options = [
            "controller" => "foo@bar",
            "middleware" => ["foo", "bar"]
        ];
        $route = new Route("get", "/foo", $options);
        $this->assertEquals(["foo", "bar"], $route->getMiddleware());
    }

    /**
     * Tests getting the raw path
     */
    public function testGettingRawPath()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/foo/{id}", $options);
        $this->assertEquals("/foo/{id}", $route->getRawPath());
    }

    /**
     * Tests getting an unset name
     */
    public function testGettingUnsetName()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $this->assertEmpty($route->getName());
    }

    /**
     * Tests getting the regex for a variable
     */
    public function testGettingVariableRegex()
    {
        $options = [
            "controller" => "foo@bar",
            "variables" => ["bar" => "\d+"]
        ];
        $route = new Route("get", "/foo", $options);
        $this->assertEquals("\d+", $route->getVariableRegex("bar"));
    }

    /**
     * Tests getting the regex for a variable that does not have a regex
     */
    public function testGettingVariableRegexForParameterWithNoRegex()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/foo", $options);
        $this->assertNull($route->getVariableRegex("bar"));
    }

    /**
     * Tests not setting the controller
     */
    public function testNotSettingController()
    {
        $this->setExpectedException("\\RuntimeException");
        new Route("get", "/{foo}", []);
    }

    /**
     * Tests not setting HTTPS
     */
    public function testNotSettingHTTPS()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $this->assertFalse($route->isSecure());
    }

    /**
     * Tests passing in a controller with no method
     */
    public function testPassingControllerWithNoMethod()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $options = [
            "controller" => "foo@"
        ];
        new Route("get", "/{foo}", $options);
    }

    /**
     * Tests passing in a controller with no name
     */
    public function testPassingControllerWithNoName()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $options = [
            "controller" => "@foo"
        ];
        new Route("get", "/{foo}", $options);
    }

    /**
     * Tests passing in an incorrectly formatted controller
     */
    public function testPassingIncorrectlyFormattedController()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $options = [
            "controller" => "foo"
        ];
        new Route("get", "/{foo}", $options);
    }

    /**
     * Tests passing in multiple methods to the constructor
     */
    public function testPassingMultipleMethodsToConstructor()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get", "post"], "/foo", $options);
        $this->assertEquals(["get", "post"], $route->getMethods());
    }

    /**
     * Tests passing in a single method to the constructor
     */
    public function testPassingSingleMethodToConstructor()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/foo", $options);
        $this->assertEquals(["get"], $route->getMethods());
    }

    /**
     * Test prepending a middleware
     */
    public function testPrependingMiddleware()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->addMiddleware("foo", false);
        $route->addMiddleware("bar", true);
        $this->assertEquals(["bar", "foo"], $route->getMiddleware());
    }

    /**
     * Tests setting the controller method
     */
    public function testSettingControllerMethod()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->setControllerMethod("blah");
        $this->assertEquals("blah", $route->getControllerMethod());
    }

    /**
     * Tests setting the controller name
     */
    public function testSettingControllerName()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->setControllerName("blah");
        $this->assertEquals("blah", $route->getControllerName());
    }

    /**
     * Tests setting HTTPS
     */
    public function testSettingHTTPS()
    {
        $options = [
            "controller" => "foo@bar",
            "https" => false
        ];
        $route = new Route("get", "/{foo}", $options);
        $this->assertFalse($route->isSecure());
        $options = [
            "controller" => "foo@bar",
            "https" => true
        ];
        $route = new Route("get", "/{foo}", $options);
        $this->assertTrue($route->isSecure());
    }

    /**
     * Tests setting multiple variable regexes
     */
    public function testSettingMultipleVariableRegexes()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}/{bar}", $options);
        $regexes = ["foo" => "\d+", "bar" => "\w+"];
        $route->setVariableRegexes($regexes);
        $this->assertEquals($regexes, $route->getVariableRegexes());
    }

    /**
     * Tests setting the name
     */
    public function testSettingName()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->setName("blah");
        $this->assertEquals("blah", $route->getName());
    }

    /**
     * Tests setting the name in the constructor
     */
    public function testSettingNameInConstructor()
    {
        $options = [
            "controller" => "foo@bar",
            "name" => "blah"
        ];
        $route = new Route("get", "/{foo}", $options);
        $this->assertEquals("blah", $route->getName());
    }

    /**
     * Tests setting the raw host
     */
    public function testSettingRawHost()
    {
        $route = new Route("get", "/foo", ["controller" => "foo@bar"]);
        $route->setRawHost("google.com");
        $this->assertEquals("google.com", $route->getRawHost());
    }

    /**
     * Tests setting the raw host in the constructor
     */
    public function testSettingRawHostInConstructor()
    {
        $options = [
            "controller" => "foo@bar",
            "host" => "google.com"
        ];
        $route = new Route("get", "/foo", $options);
        $this->assertEquals("google.com", $route->getRawHost());
    }

    /**
     * Tests setting the raw path
     */
    public function testSettingRawPath()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->setRawPath("blah");
        $this->assertEquals("blah", $route->getRawPath());
    }

    /**
     * Tests setting a variable regex
     */
    public function testSettingVariableRegex()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->setVariableRegex("foo", "\d+");
        $this->assertEquals(["foo" => "\d+"], $route->getVariableRegexes());
        $this->assertEquals("\d+", $route->getVariableRegex("foo"));
    }
} 
<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the route class
 */
namespace RDev\Models\Web\Routing;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the after filter when it is a string
     */
    public function testGettingAfterFilterWhenItIsAString()
    {
        $options = [
            "controller" => "foo@bar",
            "after" => "foo"
        ];
        $route = new Route(["get"], "/foo", $options);
        $this->assertEquals(["foo"], $route->getAfterFilters());
    }

    /**
     * Tests getting the after filters when they are an array
     */
    public function testGettingAfterFiltersWhenTheyAreAnArray()
    {
        $options = [
            "controller" => "foo@bar",
            "after" => ["foo", "bar"]
        ];
        $route = new Route(["get"], "/foo", $options);
        $this->assertEquals(["foo", "bar"], $route->getAfterFilters());
    }

    /**
     * Tests getting the before filter when it is a string
     */
    public function testGettingBeforeFilterWhenItIsAString()
    {
        $options = [
            "controller" => "foo@bar",
            "before" => "foo"
        ];
        $route = new Route(["get"], "/foo", $options);
        $this->assertEquals(["foo"], $route->getBeforeFilters());
    }

    /**
     * Tests getting the before filters when they are an array
     */
    public function testGettingBeforeFiltersWhenTheyAreAnArray()
    {
        $options = [
            "controller" => "foo@bar",
            "before" => ["foo", "bar"]
        ];
        $route = new Route(["get"], "/foo", $options);
        $this->assertEquals(["foo", "bar"], $route->getBeforeFilters());
    }

    /**
     * Tests getting the controller method
     */
    public function testGettingControllerMethod()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get"], "/{foo}", $options);
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
        $route = new Route(["get"], "/{foo}", $options);
        $this->assertEquals("foo", $route->getControllerName());
    }

    /**
     * Tests getting the default value for a variable without a default value
     */
    public function testGettingDefaultValueForVariableWithoutDefaultValue()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get"], "/{foo}", $options);
        $this->assertNull($route->getDefaultValue("foo"));
    }

    /**
     * Tests getting the methods
     */
    public function testGettingMethods()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get"], "/foo", $options);
        $this->assertEquals(["get"], $route->getMethods());
    }

    /**
     * Tests getting the raw path
     */
    public function testGettingRawPath()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get"], "/foo/{id}", $options);
        $this->assertEquals("/foo/{id}", $route->getRawPath());
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
        $route = new Route(["get"], "/foo", $options);
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
        $route = new Route(["get"], "/foo", $options);
        $this->assertNull($route->getVariableRegex("bar"));
    }

    /**
     * Tests not setting the controller
     */
    public function testNotSettingController()
    {
        $this->setExpectedException("\\RuntimeException");
        new Route(["get"], "/{foo}", []);
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
        new Route(["get"], "/{foo}", $options);
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
        new Route(["get"], "/{foo}", $options);
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
        new Route(["get"], "/{foo}", $options);
    }

    /**
     * Tests setting a default value
     */
    public function testSettingADefaultValue()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get"], "/{foo}", $options);
        $route->setDefaultValue("foo", 2);
        $this->assertEquals(2, $route->getDefaultValue("foo"));
    }

    /**
     * Tests setting the controller method
     */
    public function testSettingControllerMethod()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get"], "/{foo}", $options);
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
        $route = new Route(["get"], "/{foo}", $options);
        $route->setControllerName("blah");
        $this->assertEquals("blah", $route->getControllerName());
    }

    /**
     * Tests setting the regex
     */
    public function testSettingRegex()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get"], "/foo/{id}", $options);
        $route->setRegex("blah");
        $this->assertEquals("blah", $route->getRegex());
    }

    /**
     * Tests setting a variable regex
     */
    public function testSettingVariableRegex()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get"], "/{foo}", $options);
        $route->setVariableRegex("foo", "\d+");
        $this->assertEquals("\d+", $route->getVariableRegex("foo"));
    }
} 
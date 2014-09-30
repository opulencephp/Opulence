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
     * Tests getting the default value for a variable without a default value
     */
    public function testGettingDefaultValueForVariableWithoutDefaultValue()
    {
        $route = new Route(["get"], "/{foo}", []);
        $this->assertNull($route->getDefaultValue("foo"));
    }

    /**
     * Tests getting the methods
     */
    public function testGettingMethods()
    {
        $route = new Route(["get"], "/foo", []);
        $this->assertEquals(["get"], $route->getMethods());
    }

    /**
     * Tests getting the raw path
     */
    public function testGettingRawPath()
    {
        $route = new Route(["get"], "/foo/{id}", []);
        $this->assertEquals("/foo/{id}", $route->getRawPath());
    }

    /**
     * Tests getting the regex for a variable
     */
    public function testGettingVariableRegex()
    {
        $route = new Route(["get"], "/foo", ["variables" => ["bar" => "\d+"]]);
        $this->assertEquals("\d+", $route->getVariableRegex("bar"));
    }

    /**
     * Tests getting the regex for a variable that does not have a regex
     */
    public function testGettingVariableRegexForParameterWithNoRegex()
    {
        $route = new Route(["get"], "/foo", []);
        $this->assertNull($route->getVariableRegex("bar"));
    }

    /**
     * Tests setting a default value
     */
    public function testSettingADefaultValue()
    {
        $route = new Route(["get"], "/{foo}", []);
        $route->setDefaultValue("foo", 2);
        $this->assertEquals(2, $route->getDefaultValue("foo"));
    }

    /**
     * Tests setting the regex
     */
    public function testSettingRegex()
    {
        $route = new Route(["get"], "/foo/{id}", []);
        $route->setRegex("blah");
        $this->assertEquals("blah", $route->getRegex());
    }

    /**
     * Tests setting a variable regex
     */
    public function testSettingVariableRegex()
    {
        $route = new Route(["get"], "/{foo}", []);
        $route->setVariableRegex("foo", "\d+");
        $this->assertEquals("\d+", $route->getVariableRegex("foo"));
    }
} 
<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Tests the parsed route
 */
namespace RDev\Routing\Routes;

class ParsedRouteTest extends \PHPUnit_Framework_TestCase 
{
    /**
     * Tests creating a parsed route
     */
    public function testCreatingParsedRoute()
    {
        $route = new Route("GET", "/foo/{bar}", [
            "controller" => "foo@bar",
            "https" => true,
            "variables" => [
                "bar" => "\d+"
            ]
        ]);
        $parsedRoute = new ParsedRoute($route);
        $this->assertEquals($route->getMethods(), $parsedRoute->getMethods());
        $this->assertEquals($route->getControllerName(), $parsedRoute->getControllerName());
        $this->assertEquals($route->getControllerMethod(), $parsedRoute->getControllerMethod());
        $this->assertEquals($route->getName(), $parsedRoute->getName());
        $this->assertEquals($route->isSecure(), $parsedRoute->isSecure());
        $this->assertEquals($route->getPreFilters(), $parsedRoute->getPreFilters());
        $this->assertEquals($route->getPostFilters(), $parsedRoute->getPostFilters());
        $this->assertEquals($route->getRawHost(), $parsedRoute->getRawHost());
        $this->assertEquals($route->getRawPath(), $parsedRoute->getRawPath());
        $this->assertEquals($route->getVariableRegex("bar"), $parsedRoute->getVariableRegex("bar"));
    }

    /**
     * Tests getting the default value for a variable without a default value
     */
    public function testGettingDefaultValueForVariableWithoutDefaultValue()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $parsedRoute = new ParsedRoute($route);
        $this->assertNull($parsedRoute->getDefaultValue("foo"));
    }

    /**
     * Tests getting the host regex when it's not set
     */
    public function testGettingHostRegexWhenNotSet()
    {
        $route = new Route("get", "/foo", ["controller" => "foo@bar"]);
        $parsedRoute = new ParsedRoute($route);
        $this->assertEquals("/^.*$/", $parsedRoute->getHostRegex());
    }

    /**
     * Tests setting a default value
     */
    public function testSettingADefaultValue()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $parsedRoute = new ParsedRoute($route);
        $parsedRoute->setDefaultValue("foo", 2);
        $this->assertEquals(2, $parsedRoute->getDefaultValue("foo"));
    }

    /**
     * Tests setting the host regex
     */
    public function testSettingHostRegex()
    {
        $route = new Route("get", "/foo", ["controller" => "foo@bar"]);
        $parsedRoute = new ParsedRoute($route);
        $parsedRoute->setHostRegex("google\.com");
        $this->assertEquals("google\.com", $parsedRoute->getHostRegex());
    }

    /**
     * Tests setting the path regex
     */
    public function testSettingPathRegex()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/foo/{id}", $options);
        $parsedRoute = new ParsedRoute($route);
        $parsedRoute->setPathRegex("blah");
        $this->assertEquals("blah", $parsedRoute->getPathRegex());
    }
}
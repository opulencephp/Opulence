<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the compiled route
 */
namespace RDev\Routing\Routes;

class CompiledRouteTest extends \PHPUnit_Framework_TestCase 
{
    /** @var CompiledRoute The route to test */
    private $compiledRoute = null;
    /** @var ParsedRoute The parsed route */
    private $parsedRoute = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->parsedRoute = new ParsedRoute(new Route("GET", "/", ["controller" => "foo@bar"]));
        $this->compiledRoute = new CompiledRoute($this->parsedRoute, true, ["foo" => "bar"]);
    }

    /**
     * Tests checking if a matched route is matched
     */
    public function testCheckingIfMatched()
    {
        $this->assertTrue($this->compiledRoute->isMatch());
    }
    /**
     * Tests creating a compiled route
     */
    public function testCreatingCompiledRoute()
    {
        $route = new Route("GET", "/foo/{bar=baz}", [
            "controller" => "foo@bar",
            "https" => true,
            "variables" => [
                "bar" => "\d+"
            ]
        ]);
        $parsedRoute = new ParsedRoute($route);
        $parsedRoute->setDefaultValue("bar", "baz");
        $parsedRoute->setHostRegex("foo\.bar\.com");
        $parsedRoute->setPathRegex("baz.*");
        $compiledRoute = new CompiledRoute($parsedRoute, true, []);
        $this->assertEquals($parsedRoute->getHostRegex(), $compiledRoute->getHostRegex());
        $this->assertEquals($parsedRoute->getPathRegex(), $compiledRoute->getPathRegex());
        $this->assertEquals($parsedRoute->getDefaultValue("bar"), $compiledRoute->getDefaultValue("bar"));
    }

    /**
     * Tests getting a non-existent path variable
     */
    public function testGettingNonExistentPathVariable()
    {
        $this->assertNull($this->compiledRoute->getPathVariable("doesNotExist"));
    }

    /**
     * Tests getting a single path variable
     */
    public function testGettingPathVariable()
    {
        $this->assertEquals("bar", $this->compiledRoute->getPathVariable("foo"));
    }

    /**
     * Tests getting the path variables
     */
    public function testGettingPathVariables()
    {
        $this->assertEquals(["foo" => "bar"], $this->compiledRoute->getPathVariables());
    }

    /**
     * Tests not specifying path variables
     */
    public function testNotSpecifyingPathVariables()
    {
        $compiledRoute = new CompiledRoute($this->parsedRoute, true);
        $this->assertEquals([], $compiledRoute->getPathVariables());
    }

    /**
     * Tests setting the match
     */
    public function testSettingMatch()
    {
        $this->compiledRoute->setMatch(false);
        $this->assertFalse($this->compiledRoute->isMatch());
    }

    /**
     * Tests setting the path variables
     */
    public function testSettingPathVariables()
    {
        $this->compiledRoute->setPathVariables(["dave" => "young"]);
        $this->assertEquals(["dave" => "young"], $this->compiledRoute->getPathVariables());
    }
}
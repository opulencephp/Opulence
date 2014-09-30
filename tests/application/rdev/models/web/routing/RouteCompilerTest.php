<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the route compiler
 */
namespace RDev\Models\Web\Routing;

class RouteCompilerTest extends \PHPUnit_Framework_TestCase
{
    /** @var RouteCompiler The compiler to use in tests */
    private $compiler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->compiler = new RouteCompiler();
    }

    /**
     * Tests compiling a path with multiple variables
     */
    public function testCompilingMultipleVariables()
    {
        $route = new Route(["get"], "/{foo}/bar/{blah}", []);
        $this->compiler->compile($route);
        $this->assertEquals(
            sprintf(
                "/^%s$/",
                preg_quote("/", "/") . "(?P<foo>.+)" . preg_quote("/bar/", "/") . "(?P<blah>.+)"
            ),
            $route->getRegex()
        );
    }

    /**
     * Tests compiling a path with multiple variables with regexes
     */
    public function testCompilingMultipleVariablesWithRegexes()
    {
        $parameters = ["foo" => "\d+", "blah" => "[a-z]{3}"];
        $route = new Route(["get"], "/{foo}/bar/{blah}", ["variables" => $parameters]);
        $this->compiler->compile($route);
        $this->assertEquals(
            sprintf(
                "/^%s$/",
                preg_quote("/", "/") . "(?P<foo>\d+)" . preg_quote("/bar/", "/") . "(?P<blah>[a-z]{3})"
            ),
            $route->getRegex()
        );
    }

    /**
     * Tests compiling a path with a single variable
     */
    public function testCompilingSingleVariable()
    {
        $route = new Route(["get"], "/{foo}", []);
        $this->compiler->compile($route);
        $this->assertEquals(
            sprintf(
                "/^%s$/",
                preg_quote("/", "/") . "(?P<foo>.+)"
            ),
            $route->getRegex()
        );
    }

    /**
     * Tests compiling a path with a single variable with a default value
     */
    public function testCompilingSingleVariableWithDefaultValue()
    {
        $route = new Route(["get"], "/{foo=23}", []);
        $this->compiler->compile($route);
        $this->assertEquals(
            sprintf(
                "/^%s$/",
                preg_quote("/", "/") . "(?P<foo>.+)"
            ),
            $route->getRegex()
        );
        $this->assertEquals("23", $route->getDefaultValue("foo"));
    }

    /**
     * Tests compiling a path with a single variable with options
     */
    public function testCompilingSingleVariableWithRegexes()
    {
        $route = new Route(["get"], "/{foo}", ["variables" => ["foo" => "\d+"]]);
        $this->compiler->compile($route);
        $this->assertEquals(
            sprintf(
                "/^%s$/",
                preg_quote("/", "/") . "(?P<foo>\d+)"
            ),
            $route->getRegex()
        );
    }

    /**
     * Tests compiling a static path
     */
    public function testCompilingStaticPath()
    {
        $path = "/foo/bar/blah";
        $route = new Route(["get"], $path, []);
        $this->compiler->compile($route);
        $this->assertEquals(
            sprintf(
                "/^%s$/",
                preg_quote($path, "/")
            ),
            $route->getRegex()
        );
    }

    /**
     * Tests compiling a path with duplicate variables
     */
    public function testCompilingWithDuplicateVariables()
    {
        $this->setExpectedException("\\RuntimeException");
        $route = new Route(["get"], "/{foo}/{foo}", []);
        $this->compiler->compile($route);
    }
} 
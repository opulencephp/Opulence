<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the route compiler
 */
namespace RDev\Models\Routing;

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
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get"], "/{foo}/bar/{blah}", $options);
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
        $options = [
            "controller" => "foo@bar",
            "variables" => [
                "foo" => "\d+",
                "blah" => "[a-z]{3}"
            ]
        ];
        $route = new Route(["get"], "/{foo}/bar/{blah}", $options);
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
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get"], "/{foo}", $options);
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
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get"], "/{foo=23}", $options);
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
        $options = [
            "controller" => "foo@bar",
            "variables" => ["foo" => "\d+"]
        ];
        $route = new Route(["get"], "/{foo}", $options);
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
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get"], $path, $options);
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
        $this->setExpectedException("RDev\\Models\\Routing\\Exceptions\\RouteException");
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get"], "/{foo}/{foo}", $options);
        $this->compiler->compile($route);
    }

    /**
     * Tests compiling a path with an unclosed open brace
     */
    public function testCompilingWithUnclosedOpenBrace()
    {
        $this->setExpectedException("RDev\\Models\\Routing\\Exceptions\\RouteException");
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get"], "/{foo}/{bar", $options);
        $this->compiler->compile($route);
    }

    /**
     * Tests compiling a path with an unopened close brace
     */
    public function testCompilingWithUnopenedCloseBrace()
    {
        $this->setExpectedException("RDev\\Models\\Routing\\Exceptions\\RouteException");
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get"], "/{foo}/{bar}}", $options);
        $this->compiler->compile($route);
    }

    /**
     * Tests using a route variable with a name that isn't a valid PHP variable name
     */
    public function testInvalidPHPVariableName()
    {
        $this->setExpectedException("RDev\\Models\\Routing\\Exceptions\\RouteException");
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get"], "/{123foo}/bar", $options);
        $this->compiler->compile($route);
    }

    /**
     * Tests an optional variable
     */
    public function testOptionalVariable()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get"], "/{foo}/bar/{blah?}", $options);
        $this->compiler->compile($route);
        $this->assertEquals(
            sprintf(
                "/^%s$/",
                preg_quote("/", "/") . "(?P<foo>.+)" . preg_quote("/bar/", "/") . "(?P<blah>.+)?"
            ),
            $route->getRegex()
        );
    }

    /**
     * Tests an optional variable with a default value
     */
    public function testOptionalVariableWithDefaultValue()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get"], "/{foo}/bar/{blah?=123}", $options);
        $this->compiler->compile($route);
        $this->assertEquals(
            sprintf(
                "/^%s$/",
                preg_quote("/", "/") . "(?P<foo>.+)" . preg_quote("/bar/", "/") . "(?P<blah>.+)?"
            ),
            $route->getRegex()
        );
        $this->assertEquals("123", $route->getDefaultValue("blah"));
    }
} 
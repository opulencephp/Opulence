<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the route compiler
 */
namespace RDev\Routing;

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
        $rawString = "/{foo}/bar/{blah}";
        $options = [
            "controller" => "foo@bar",
            "host" => $rawString
        ];
        $route = new Route(["get"], $rawString, $options);
        $this->compiler->compile($route);
        $this->assertTrue(
            $this->regexesMach(
                $route,
                sprintf(
                    "/^%s$/",
                    preg_quote("/", "/") . "(?P<foo>.+)" . preg_quote("/bar/", "/") . "(?P<blah>.+)"
                )
            )
        );
    }

    /**
     * Tests compiling a path with multiple variables with regexes
     */
    public function testCompilingMultipleVariablesWithRegexes()
    {
        $rawString = "/{foo}/bar/{blah}";
        $options = [
            "controller" => "foo@bar",
            "variables" => [
                "foo" => "\d+",
                "blah" => "[a-z]{3}"
            ],
            "host" => $rawString
        ];
        $route = new Route(["get"], $rawString, $options);
        $this->compiler->compile($route);
        $this->assertTrue(
            $this->regexesMach(
                $route,
                sprintf(
                    "/^%s$/",
                    preg_quote("/", "/") . "(?P<foo>\d+)" . preg_quote("/bar/", "/") . "(?P<blah>[a-z]{3})"
                )
            )
        );
    }

    /**
     * Tests compiling a path with a single variable
     */
    public function testCompilingSingleVariable()
    {
        $rawString = "/{foo}";
        $options = [
            "controller" => "foo@bar",
            "host" => $rawString
        ];
        $route = new Route(["get"], $rawString, $options);
        $this->compiler->compile($route);
        $this->assertTrue(
            $this->regexesMach(
                $route,
                sprintf(
                    "/^%s$/",
                    preg_quote("/", "/") . "(?P<foo>.+)"
                )
            )
        );
    }

    /**
     * Tests compiling a path with a single variable with a default value
     */
    public function testCompilingSingleVariableWithDefaultValue()
    {
        $rawString = "/{foo=23}";
        $options = [
            "controller" => "foo@bar",
            "host" => $rawString
        ];
        $route = new Route(["get"], $rawString, $options);
        $this->compiler->compile($route);
        $this->assertTrue(
            $this->regexesMach(
                $route,
                sprintf(
                    "/^%s$/",
                    preg_quote("/", "/") . "(?P<foo>.+)"
                )
            )
        );
        $this->assertEquals("23", $route->getDefaultValue("foo"));
    }

    /**
     * Tests compiling a path with a single variable with options
     */
    public function testCompilingSingleVariableWithRegexes()
    {
        $rawString = "/{foo}";
        $options = [
            "controller" => "foo@bar",
            "variables" => ["foo" => "\d+"],
            "host" => $rawString
        ];
        $route = new Route(["get"], $rawString, $options);
        $this->compiler->compile($route);
        $this->assertTrue(
            $this->regexesMach(
                $route,
                sprintf(
                    "/^%s$/",
                    preg_quote("/", "/") . "(?P<foo>\d+)"
                )
            )
        );
    }

    /**
     * Tests compiling a static path
     */
    public function testCompilingStaticPath()
    {
        $rawString = "/foo/bar/blah";
        $options = [
            "controller" => "foo@bar",
            "host" => $rawString
        ];
        $route = new Route(["get"], $rawString, $options);
        $this->compiler->compile($route);
        $this->assertTrue(
            $this->regexesMach(
                $route,
                sprintf(
                    "/^%s$/",
                    preg_quote($rawString, "/")
                )
            )
        );
    }

    /**
     * Tests compiling a path with duplicate variables
     */
    public function testCompilingWithDuplicateVariables()
    {
        $this->setExpectedException("RDev\\Routing\\RouteException");
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
        $this->setExpectedException("RDev\\Routing\\RouteException");
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
        $this->setExpectedException("RDev\\Routing\\RouteException");
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
        $this->setExpectedException("RDev\\Routing\\RouteException");
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get"], "/{123foo}/bar", $options);
        $this->compiler->compile($route);
    }

    /**
     * Tests not specifying a host
     */
    public function testNotSpecifyingHost()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get"], "/foo", $options);
        $this->compiler->compile($route);
        $this->assertEquals("/^.*$/", $route->getHostRegex());
    }

    /**
     * Tests an optional variable
     */
    public function testOptionalVariable()
    {
        $rawString = "/{foo}/bar/{blah?}";
        $options = [
            "controller" => "foo@bar",
            "host" => $rawString
        ];
        $route = new Route(["get"], $rawString, $options);
        $this->compiler->compile($route);
        $this->assertTrue(
            $this->regexesMach(
                $route,
                sprintf(
                    "/^%s$/",
                    preg_quote("/", "/") . "(?P<foo>.+)" . preg_quote("/bar/", "/") . "(?P<blah>.+)?"
                )
            )
        );
    }

    /**
     * Tests an optional variable with a default value
     */
    public function testOptionalVariableWithDefaultValue()
    {
        $rawString = "/{foo}/bar/{blah?=123}";
        $options = [
            "controller" => "foo@bar",
            "host" => $rawString
        ];
        $route = new Route(["get"], $rawString, $options);
        $this->compiler->compile($route);
        $this->assertTrue(
            $this->regexesMach(
                $route,
                sprintf(
                    "/^%s$/",
                    preg_quote("/", "/") . "(?P<foo>.+)" . preg_quote("/bar/", "/") . "(?P<blah>.+)?"
                )
            )
        );
        $this->assertEquals("123", $route->getDefaultValue("blah"));
    }

    /**
     * Tests specifying an empty path
     */
    public function testSpecifyingEmptyPath()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get"], "", $options);
        $this->compiler->compile($route);
        $this->assertEquals("/^.*$/", $route->getPathRegex());
    }

    /**
     * Gets whether or not a route's regexes match the input regex
     *
     * @param Route $route The route whose regexes we're matching
     * @param string $regex The expected regex
     * @return bool True if the regexes match, otherwise false
     */
    private function regexesMach(Route $route, $regex)
    {
        return $route->getPathRegex() == $regex && $route->getHostRegex() == $regex;
    }
} 
<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the URL generator
 */
namespace Opulence\Routing\URL;

use Opulence\HTTP\Requests\Request;
use Opulence\Routing\Routes\Compilers\Parsers\Parser;
use Opulence\Routing\Routes\Route;
use Opulence\Routing\Routes\RouteCollection;

class URLGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var URLGenerator The generator to use in tests */
    private $generator = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $namedRoutes = [
            new Route(
                Request::METHOD_GET,
                "/users",
                "foo@bar",
                ["name" => "pathNoParameters"]
            ),
            new Route(
                Request::METHOD_GET,
                "/users/:userId",
                "foo@bar",
                ["name" => "pathOneParameter"]
            ),
            new Route(
                Request::METHOD_GET,
                "/users/:userId/profile/:mode",
                "foo@bar",
                ["name" => "pathTwoParameters"]
            ),
            new Route(
                Request::METHOD_GET,
                "/users[:foo]",
                "foo@bar",
                ["name" => "pathOptionalVariable"]
            ),
            new Route(
                Request::METHOD_GET,
                "/users/:userId",
                "foo@bar",
                [
                    "variables" => ["userId" => "\d+"],
                    "name" => "pathVariableRegex"
                ]
            ),
            new Route(
                Request::METHOD_GET,
                "/users",
                "foo@bar",
                [
                    "host" => "example.com",
                    "name" => "hostNoParameters"
                ]
            ),
            new Route(
                Request::METHOD_GET,
                "/users",
                "foo@bar",
                [
                    "host" => ":subdomain.example.com",
                    "name" => "hostOneParameter"
                ]
            ),
            new Route(
                Request::METHOD_GET,
                "/users",
                "foo@bar",
                [
                    "host" => ":subdomain1.:subdomain2.example.com",
                    "name" => "hostTwoParameters"
                ]
            ),
            new Route(
                Request::METHOD_GET,
                "/users",
                "foo@bar",
                [
                    "host" => "[:subdomain]example.com",
                    "name" => "hostOptionalVariable"
                ]
            ),
            new Route(
                Request::METHOD_GET,
                "/users/:userId/profile/:mode",
                "foo@bar",
                [
                    "host" => ":subdomain1.:subdomain2.example.com",
                    "name" => "hostAndPathMultipleParameters"
                ]
            ),
            new Route(
                Request::METHOD_GET,
                "/users[:foo]",
                "foo@bar",
                [
                    "host" => "[:subdomain]example.com",
                    "name" => "hostAndPathOptionalParameters"
                ]
            ),
            new Route(
                Request::METHOD_GET,
                "/users",
                "foo@bar",
                [
                    "host" => "foo.example.com",
                    "https" => true,
                    "name" => "secureHostNoParameters"
                ]
            )
        ];
        $routeCollection = new RouteCollection();
        $parser = new Parser();

        foreach($namedRoutes as $name => $route)
        {
            $routeCollection->add($parser->parse($route));
        }

        $parser = new Parser();
        $this->generator = new URLGenerator($routeCollection, $parser->getVarMatchingRegex());
    }

    /**
     * Tests generating an HTTPs URL
     */
    public function testGeneratingHTTPSURL()
    {
        $this->assertEquals(
            "https://foo.example.com/users",
            $this->generator->createFromName("secureHostNoParameters")
        );
    }

    /**
     * Tests generating a route for a non-existent route
     */
    public function testGeneratingURLForNonExistentRoute()
    {
        $this->assertEmpty($this->generator->createFromName("foo"));
    }

    /**
     * Tests generating a URL with multiple host and path values
     */
    public function testGeneratingURLWithMultipleHostAndPathValues()
    {
        $this->assertEquals(
            "http://foo.bar.example.com/users/23/profile/edit",
            $this->generator->createFromName("hostAndPathMultipleParameters", ["foo", "bar", 23, "edit"])
        );
    }

    /**
     * Tests generating a URL with no values
     */
    public function testGeneratingURLWithNoValues()
    {
        $this->assertEquals("/users", $this->generator->createFromName("pathNoParameters"));
        $this->assertEquals("http://example.com/users", $this->generator->createFromName("hostNoParameters"));
    }

    /**
     * Tests generating a URL with one value
     */
    public function testGeneratingURLWithOneValue()
    {
        $this->assertEquals("/users/23", $this->generator->createFromName("pathOneParameter", [23]));
        $this->assertEquals("http://foo.example.com/users", $this->generator->createFromName("hostOneParameter", ["foo"]));
    }

    /**
     * Tests generating a URL with an optional host variable
     */
    public function testGeneratingURLWithOptionalHostVariable()
    {
        $this->assertEquals(
            "http://example.com/users",
            $this->generator->createFromName("hostOptionalVariable")
        );
    }

    /**
     * Tests generating a URL with an optional path variable
     */
    public function testGeneratingURLWithOptionalPathVariable()
    {
        $this->assertEquals(
            "/users",
            $this->generator->createFromName("pathOptionalVariable")
        );
    }

    /**
     * Tests generating a URL with optional variables in the path and host
     */
    public function testGeneratingURLWithOptionalVariablesInPathAndHost()
    {
        $this->assertEquals(
            "http://example.com/users",
            $this->generator->createFromName("hostAndPathOptionalParameters")
        );
    }

    /**
     * Tests generating a URL with two values
     */
    public function testGeneratingURLWithTwoValues()
    {
        $this->assertEquals("/users/23/profile/edit", $this->generator->createFromName("pathTwoParameters", [23, "edit"]));
        $this->assertEquals(
            "http://foo.bar.example.com/users",
            $this->generator->createFromName("hostTwoParameters", ["foo", "bar"])
        );
    }

    /**
     * Tests generating a URL with a variable value that does not satisfy the regex
     */
    public function testGeneratingURLWithVariableThatDoesNotSatisfyRegex()
    {
        $this->setExpectedException(URLException::class);
        $this->generator->createFromName("pathVariableRegex", "notANumber");
    }

    /**
     * Tests not filling all values in a host
     */
    public function testNotFillingAllHostValues()
    {
        $this->setExpectedException(URLException::class);
        $this->generator->createFromName("hostOneParameter");

    }

    /**
     * Tests not filling all values in a path
     */
    public function testNotFillingAllPathValues()
    {
        $this->setExpectedException(URLException::class);
        $this->generator->createFromName("pathOneParameter");
    }

    /**
     * Tests passing in a non array value
     */
    public function testPassingNonArrayValue()
    {
        $this->assertEquals("/users/23", $this->generator->createFromName("pathOneParameter", 23));
    }
}
<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Tests the URL generator
 */
namespace RDev\Routing\URL;
use RDev\HTTP;
use RDev\Routing;
use RDev\Routing\Compilers;

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
            "pathNoParameters" => new Routing\Route(
                HTTP\Request::METHOD_GET,
                "/users",
                ["controller" => "foo@bar"]
            ),
            "pathOneParameter" => new Routing\Route(
                HTTP\Request::METHOD_GET,
                "/users/{userId}",
                ["controller" => "foo@bar"]
            ),
            "pathTwoParameters" => new Routing\Route(
                HTTP\Request::METHOD_GET,
                "/users/{userId}/profile/{mode}",
                ["controller" => "foo@bar"]
            ),
            "hostNoParameters" => new Routing\Route(
                HTTP\Request::METHOD_GET,
                "/users",
                ["controller" => "foo@bar", "host" => "example.com"]
            ),
            "hostOneParameter" => new Routing\Route(
                HTTP\Request::METHOD_GET,
                "/users",
                ["controller" => "foo@bar", "host" => "{subdomain}.example.com"]
            ),
            "hostTwoParameters" => new Routing\Route(
                HTTP\Request::METHOD_GET,
                "/users",
                ["controller" => "foo@bar", "host" => "{subdomain1}.{subdomain2}.example.com"]
            ),
            "hostAndPathMultipleParameters" => new Routing\Route(
                HTTP\Request::METHOD_GET,
                "/users/{userId}/profile/{mode}",
                ["controller" => "foo@bar", "host" => "{subdomain1}.{subdomain2}.example.com"]
            ),
            "secureHostNoParameters" => new Routing\Route(
                HTTP\Request::METHOD_GET,
                "/users",
                ["controller" => "foo@bar", "host" => "foo.example.com", "https" => true]
            )
        ];
        $this->generator = new URLGenerator(new Compilers\Compiler(), $namedRoutes);
    }

    /**
     * Tests generating an HTTPs URL
     */
    public function testGeneratingHTTPSURL()
    {
        $this->assertEquals(
            "https://foo.example.com/users",
            $this->generator->generate("secureHostNoParameters")
        );
    }

    /**
     * Tests generating a route for a non-existent route
     */
    public function testGeneratingURLForNonExistentRoute()
    {
        $this->assertEmpty($this->generator->generate("foo"));
    }

    /**
     * Tests generating a URL with multiple host and path values
     */
    public function testGeneratingURLWithMultipleHostAndPathValues()
    {
        $this->assertEquals(
            "http://foo.bar.example.com/users/23/profile/edit",
            $this->generator->generate("hostAndPathMultipleParameters", ["foo", "bar", 23, "edit"])
        );
    }

    /**
     * Tests generating a URL with no values
     */
    public function testGeneratingURLWithNoValues()
    {
        $this->assertEquals("/users", $this->generator->generate("pathNoParameters"));
        $this->assertEquals("http://example.com/users", $this->generator->generate("hostNoParameters"));
    }

    /**
     * Tests generating a URL with one value
     */
    public function testGeneratingURLWithOneValue()
    {
        $this->assertEquals("/users/23", $this->generator->generate("pathOneParameter", [23]));
        $this->assertEquals("http://foo.example.com/users", $this->generator->generate("hostOneParameter", ["foo"]));
    }

    /**
     * Tests generating a URL with two values
     */
    public function testGeneratingURLWithTwoValues()
    {
        $this->assertEquals("/users/23/profile/edit", $this->generator->generate("pathTwoParameters", [23, "edit"]));
        $this->assertEquals(
            "http://foo.bar.example.com/users",
            $this->generator->generate("hostTwoParameters", ["foo", "bar"])
        );
    }

    /**
     * Tests not filling all values in a host
     */
    public function testNotFillingAllHostValues()
    {
        $this->setExpectedException("RDev\\Routing\\URL\\URLException");
        $this->generator->generate("hostOneParameter");
    }

    /**
     * Tests not filling all values in a path
     */
    public function testNotFillingAllPathValues()
    {
        $this->setExpectedException("RDev\\Routing\\URL\\URLException");
        $this->generator->generate("pathOneParameter");
    }

    /**
     * Tests passing in a non array value
     */
    public function testPassingNonArrayValue()
    {
        $this->assertEquals("/users/23", $this->generator->generate("pathOneParameter", 23));
    }
}
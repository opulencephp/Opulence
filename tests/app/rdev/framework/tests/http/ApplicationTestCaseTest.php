<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the HTTP application tester
 */
namespace RDev\Framework\Tests\HTTP;
use RDev\HTTP\Responses\ResponseHeaders;
use RDev\Tests\Framework\Tests\HTTP\Mocks\ApplicationTestCase;

class ApplicationTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /** @var ApplicationTestCase The HTTP application to use in tests */
    private $testCase = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->testCase = new ApplicationTestCase();
        $this->testCase->setUp();
        $router = $this->testCase->getRouter();
        $router->group(["controllerNamespace" => "RDev\\Tests\\HTTP\\Routing\\Mocks"], function() use ($router)
        {
            $router->get("/badgateway", ["controller" => "HTTPApplicationTestController@setBadGateway"]);
            $router->get("/cookie", ["controller" => "HTTPApplicationTestController@setCookie"]);
            $router->get("/foobar", ["controller" => "HTTPApplicationTestController@showFooBar"]);
            $router->get("/header", ["controller" => "HTTPApplicationTestController@setHeader"]);
            $router->get("/ise", ["controller" => "HTTPApplicationTestController@setISE"]);
            $router->get("/ok", ["controller" => "HTTPApplicationTestController@setOK"]);
            $router->get("/redirect", ["controller" => "HTTPApplicationTestController@redirect"]);
            $router->get("/settag", ["controller" => "HTTPApplicationTestController@setTag"]);
            $router->get("/setvar", ["controller" => "HTTPApplicationTestController@setVar"]);
            $router->get("/unauthorized", ["controller" => "HTTPApplicationTestController@setUnauthorized"]);
        });
    }

    /**
     * Tests asserting that a template has a tag
     */
    public function testAssertTemplateHasTag()
    {
        $this->testCase->route("GET", "/settag");
        $this->testCase->assertTemplateHasTag("foo");
        $this->testCase->assertTemplateTagEquals("foo", "bar");
    }

    /**
     * Tests asserting that a template has a variable
     */
    public function testAssertTemplateHasVariable()
    {
        $this->testCase->route("GET", "/setvar");
        $this->testCase->assertTemplateHasVar("foo");
        $this->testCase->assertTemplateVarEquals("foo", "bar");
    }

    /**
     * Tests asserting that a path redirects to another
     */
    public function testAssertingRedirect()
    {
        $this->testCase->route("GET", "/redirect");
        $this->testCase->assertRedirectsTo("/redirectedPath");
    }

    /**
     * Tests asserting that a response has certain content
     */
    public function testAssertingResponseHasContent()
    {
        $this->testCase->route("GET", "/foobar");
        $this->testCase->assertResponseContentEquals("FooBar");
    }

    /**
     * Tests asserting that a response has a certain cookie
     */
    public function testAssertingResponseHasCookie()
    {
        $this->testCase->route("GET", "/cookie");
        $this->testCase->assertResponseHasCookie("foo");
        $this->testCase->assertResponseCookieValueEquals("foo", "bar");
    }

    /**
     * Tests asserting that a response has a certain header
     */
    public function testAssertingResponseHasHeader()
    {
        $this->testCase->route("GET", "/header");
        $this->testCase->assertResponseHasHeader("foo");
        $this->testCase->assertResponseHeaderEquals("foo", "bar");
    }

    /**
     * Tests asserting that a response has status code
     */
    public function testAssertingResponseHasStatusCode()
    {
        $this->testCase->route("GET", "/badgateway");
        $this->testCase->assertResponseStatusCodeEquals(ResponseHeaders::HTTP_BAD_GATEWAY);
    }

    /**
     * Tests asserting that a response is an internal server error
     */
    public function testAssertingResponseIsInternalServerError()
    {
        $this->testCase->route("GET", "/ise");
        $this->testCase->assertResponseIsInternalServerError();
    }

    /**
     * Tests asserting that a response is not found
     */
    public function testAssertingResponseIsNotFound()
    {
        $this->testCase->route("GET", "/notfound");
        $this->testCase->assertResponseIsNotFound();
    }

    /**
     * Tests asserting that a response is OK
     */
    public function testAssertingResponseIsOK()
    {
        $this->testCase->route("GET", "/ok");
        $this->testCase->assertResponseIsOK();
    }

    /**
     * Tests asserting that a response is unauthorized
     */
    public function testAssertingResponseIsUnauthorized()
    {
        $this->testCase->route("GET", "/unauthorized");
        $this->testCase->assertResponseIsUnauthorized();
    }

    /**
     * Tests getting the application
     */
    public function testGettingApplication()
    {
        $this->assertInstanceOf("RDev\\Applications\\Application", $this->testCase->getApplication());
    }

    /**
     * Tests getting the router
     */
    public function testGettingRouter()
    {
        $this->assertInstanceOf("RDev\\HTTP\\Routing\\Router", $this->testCase->getRouter());
    }
}
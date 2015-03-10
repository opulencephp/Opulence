<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the HTTP application tester
 */
namespace RDev\Framework\Tests;
use RDev\HTTP\Responses;
use RDev\HTTP\Routing\Routes;
use RDev\Tests\Framework\Tests\Mocks;

class HTTPApplicationTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /** @var Mocks\HTTPApplicationTestCase The HTTP application to use in tests */
    private $httpApplication = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->httpApplication = new Mocks\HTTPApplicationTestCase();
        $this->httpApplication->setUp();
        $router = $this->httpApplication->getRouter();
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
        $this->httpApplication->route("GET", "/settag");
        $this->httpApplication->assertTemplateHasTag("foo");
        $this->httpApplication->assertTemplateTagEquals("foo", "bar");
    }

    /**
     * Tests asserting that a template has a variable
     */
    public function testAssertTemplateHasVariable()
    {
        $this->httpApplication->route("GET", "/setvar");
        $this->httpApplication->assertTemplateHasVar("foo");
        $this->httpApplication->assertTemplateVarEquals("foo", "bar");
    }

    /**
     * Tests asserting that a path redirects to another
     */
    public function testAssertingRedirect()
    {
        $this->httpApplication->route("GET", "/redirect");
        $this->httpApplication->assertRedirectsTo("/redirectedPath");
    }

    /**
     * Tests asserting that a response has certain content
     */
    public function testAssertingResponseHasContent()
    {
        $this->httpApplication->route("GET", "/foobar");
        $this->httpApplication->assertResponseContentEquals("FooBar");
    }

    /**
     * Tests asserting that a response has a certain cookie
     */
    public function testAssertingResponseHasCookie()
    {
        $this->httpApplication->route("GET", "/cookie");
        $this->httpApplication->assertResponseHasCookie("foo");
        $this->httpApplication->assertResponseCookieValueEquals("foo", "bar");
    }

    /**
     * Tests asserting that a response has a certain header
     */
    public function testAssertingResponseHasHeader()
    {
        $this->httpApplication->route("GET", "/header");
        $this->httpApplication->assertResponseHasHeader("foo");
        $this->httpApplication->assertResponseHeaderEquals("foo", "bar");
    }

    /**
     * Tests asserting that a response has status code
     */
    public function testAssertingResponseHasStatusCode()
    {
        $this->httpApplication->route("GET", "/badgateway");
        $this->httpApplication->assertResponseStatusCodeEquals(Responses\ResponseHeaders::HTTP_BAD_GATEWAY);
    }

    /**
     * Tests asserting that a response is an internal server error
     */
    public function testAssertingResponseIsInternalServerError()
    {
        $this->httpApplication->route("GET", "/ise");
        $this->httpApplication->assertResponseIsInternalServerError();
    }

    /**
     * Tests asserting that a response is not found
     */
    public function testAssertingResponseIsNotFound()
    {
        $this->httpApplication->route("GET", "/notfound");
        $this->httpApplication->assertResponseIsNotFound();
    }

    /**
     * Tests asserting that a response is OK
     */
    public function testAssertingResponseIsOK()
    {
        $this->httpApplication->route("GET", "/ok");
        $this->httpApplication->assertResponseIsOK();
    }

    /**
     * Tests asserting that a response is unauthorized
     */
    public function testAssertingResponseIsUnauthorized()
    {
        $this->httpApplication->route("GET", "/unauthorized");
        $this->httpApplication->assertResponseIsUnauthorized();
    }

    /**
     * Tests getting the application
     */
    public function testGettingApplication()
    {
        $this->assertInstanceOf("RDev\\Applications\\Application", $this->httpApplication->getApplication());
    }

    /**
     * Tests getting the kernel
     */
    public function testGettingKernel()
    {
        $this->assertInstanceOf("RDev\\HTTP\\Kernels\\Kernel", $this->httpApplication->getKernel());
    }

    /**
     * Tests getting the router
     */
    public function testGettingRouter()
    {
        $this->assertInstanceOf("RDev\\HTTP\\Routing\\Router", $this->httpApplication->getRouter());
    }
}
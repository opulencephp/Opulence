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
    private $application = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->application = new Mocks\HTTPApplicationTestCase();
        $this->application->setUp();
        $router = $this->application->getRouter();
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
        $this->application->route("GET", "/settag");
        $this->application->assertTemplateHasTag("foo");
        $this->application->assertTemplateTagEquals("foo", "bar");
    }

    /**
     * Tests asserting that a template has a variable
     */
    public function testAssertTemplateHasVariable()
    {
        $this->application->route("GET", "/setvar");
        $this->application->assertTemplateHasVar("foo");
        $this->application->assertTemplateVarEquals("foo", "bar");
    }

    /**
     * Tests asserting that a path redirects to another
     */
    public function testAssertingRedirect()
    {
        $this->application->route("GET", "/redirect");
        $this->application->assertRedirectsTo("/redirectedPath");
    }

    /**
     * Tests asserting that a response has certain content
     */
    public function testAssertingResponseHasContent()
    {
        $this->application->route("GET", "/foobar");
        $this->application->assertResponseContentEquals("FooBar");
    }

    /**
     * Tests asserting that a response has a certain cookie
     */
    public function testAssertingResponseHasCookie()
    {
        $this->application->route("GET", "/cookie");
        $this->application->assertResponseHasCookie("foo");
        $this->application->assertResponseCookieValueEquals("foo", "bar");
    }

    /**
     * Tests asserting that a response has a certain header
     */
    public function testAssertingResponseHasHeader()
    {
        $this->application->route("GET", "/header");
        $this->application->assertResponseHasHeader("foo");
        $this->application->assertResponseHeaderEquals("foo", "bar");
    }

    /**
     * Tests asserting that a response has status code
     */
    public function testAssertingResponseHasStatusCode()
    {
        $this->application->route("GET", "/badgateway");
        $this->application->assertResponseStatusCodeEquals(Responses\ResponseHeaders::HTTP_BAD_GATEWAY);
    }

    /**
     * Tests asserting that a response is an internal server error
     */
    public function testAssertingResponseIsInternalServerError()
    {
        $this->application->route("GET", "/ise");
        $this->application->assertResponseIsInternalServerError();
    }

    /**
     * Tests asserting that a response is not found
     */
    public function testAssertingResponseIsNotFound()
    {
        $this->application->route("GET", "/notfound");
        $this->application->assertResponseIsNotFound();
    }

    /**
     * Tests asserting that a response is OK
     */
    public function testAssertingResponseIsOK()
    {
        $this->application->route("GET", "/ok");
        $this->application->assertResponseIsOK();
    }

    /**
     * Tests asserting that a response is unauthorized
     */
    public function testAssertingResponseIsUnauthorized()
    {
        $this->application->route("GET", "/unauthorized");
        $this->application->assertResponseIsUnauthorized();
    }

    /**
     * Tests getting the application
     */
    public function testGettingApplication()
    {
        $this->assertInstanceOf("RDev\\Applications\\Application", $this->application->getApplication());
    }

    /**
     * Tests getting the kernel
     */
    public function testGettingKernel()
    {
        $this->assertInstanceOf("RDev\\HTTP\\Kernels\\Kernel", $this->application->getKernel());
    }

    /**
     * Tests getting the router
     */
    public function testGettingRouter()
    {
        $this->assertInstanceOf("RDev\\HTTP\\Routing\\Router", $this->application->getRouter());
    }
}
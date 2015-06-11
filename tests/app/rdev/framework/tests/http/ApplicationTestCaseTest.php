<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the HTTP application tester
 */
namespace RDev\Framework\Tests\HTTP;
use LogicException;
use RDev\Applications\Application;
use RDev\Applications\Environments\Environment;
use RDev\Framework\HTTP\Kernel;
use RDev\HTTP\Responses\Response;
use RDev\HTTP\Responses\ResponseHeaders;
use RDev\Routing\Router;
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
        $router->get("/closure-controller", function ()
        {
            return new Response("Closure");
        });
        $router->group(["controllerNamespace" => "RDev\\Tests\\Routing\\Mocks"], function () use ($router)
        {
            $router->get("/badgateway", "HTTPApplicationTestController@setBadGateway");
            $router->get("/cookie", "HTTPApplicationTestController@setCookie");
            $router->get("/foobar", "HTTPApplicationTestController@showFooBar");
            $router->get("/header", "HTTPApplicationTestController@setHeader");
            $router->get("/ise", "HTTPApplicationTestController@setISE");
            $router->get("/ok", "HTTPApplicationTestController@setOK");
            $router->get("/redirect", "HTTPApplicationTestController@redirect");
            $router->get("/settag", "HTTPApplicationTestController@setTag");
            $router->get("/setvar", "HTTPApplicationTestController@setVar");
            $router->get("/unauthorized", "HTTPApplicationTestController@setUnauthorized");
            $router->get("/non-rdev-controller", "NonRDevController@showFoo");
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
        $this->assertInstanceOf(Application::class, $this->testCase->getApplication());
    }

    /**
     * Tests getting the router
     */
    public function testGettingRouter()
    {
        $this->assertInstanceOf(Router::class, $this->testCase->getRouter());
    }

    /**
     * Tests that the kernel instance is returned
     */
    public function testKernelInstanceIsReturned()
    {
        $this->assertInstanceOf(Kernel::class, $this->testCase->getKernel());
    }

    /**
     * Tests that a logic exception is thrown if checking if a template has a tag when using a closure controller
     */
    public function testLogicExceptionCheckingIfTemplateHasTagFromNonClosureController()
    {
        $this->setExpectedException(LogicException::class);
        $this->testCase->route("GET", "/closure-controller");
        $this->testCase->assertTemplateHasTag("foo");
    }

    /**
     * Tests that a logic exception is thrown if checking if a template has a tag when using a non-RDev controller
     */
    public function testLogicExceptionCheckingIfTemplateHasTagFromNonRDevController()
    {
        $this->setExpectedException(LogicException::class);
        $this->testCase->route("GET", "/non-rdev-controller");
        $this->testCase->assertTemplateHasTag("foo");
    }

    /**
     * Tests that a logic exception is thrown if checking if a template has a variable when using a non-RDev controller
     */
    public function testLogicExceptionCheckingIfTemplateHasVariableFromNonRDevController()
    {
        $this->setExpectedException(LogicException::class);
        $this->testCase->route("GET", "/non-rdev-controller");
        $this->testCase->assertTemplateHasVar("foo");
    }

    /**
     * Tests that a logic exception is thrown if getting a template tag when using a non-RDev controller
     */
    public function testLogicExceptionGettingTemplateTagFromNonRDevController()
    {
        $this->setExpectedException(LogicException::class);
        $this->testCase->route("GET", "/non-rdev-controller");
        $this->testCase->assertTemplateTagEquals("bar", "foo");
    }

    /**
     * Tests that a logic exception is thrown if getting a template variable when using a non-RDev controller
     */
    public function testLogicExceptionGettingTemplateVariableFromNonRDevController()
    {
        $this->setExpectedException(LogicException::class);
        $this->testCase->route("GET", "/non-rdev-controller");
        $this->testCase->assertTemplateVarEquals("bar", "foo");
    }

    /**
     * Tests that the testing environment is set
     */
    public function testTestingEnvironmentIsSet()
    {
        $this->assertEquals(Environment::TESTING, $this->testCase->getApplication()->getEnvironment()->getName());
    }
}
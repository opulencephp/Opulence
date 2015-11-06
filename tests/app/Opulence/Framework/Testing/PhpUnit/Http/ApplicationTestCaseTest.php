<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Testing\PhpUnit\Http;

use LogicException;
use Opulence\Applications\Application;
use Opulence\Applications\Environments\Environment;
use Opulence\Framework\Http\Kernel;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;
use Opulence\Routing\Router;
use Opulence\Tests\Framework\Testing\PhpUnit\Http\Mocks\ApplicationTestCase;

/**
 * Tests the HTTP application tester
 */
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
        $router->get("/closure-controller", function () {
            return new Response("Closure");
        });
        $router->group(["controllerNamespace" => "Opulence\\Tests\\Routing\\Mocks"], function () use ($router) {
            $router->get("/badgateway", "HttpApplicationTestController@setBadGateway");
            $router->get("/cookie", "HttpApplicationTestController@setCookie");
            $router->get("/foobar", "HttpApplicationTestController@showFooBar");
            $router->get("/header", "HttpApplicationTestController@setHeader");
            $router->get("/ise", "HttpApplicationTestController@setISE");
            $router->get("/ok", "HttpApplicationTestController@setOK");
            $router->get("/redirect", "HttpApplicationTestController@redirect");
            $router->get("/setvar", "HttpApplicationTestController@setVar");
            $router->get("/unauthorized", "HttpApplicationTestController@setUnauthorized");
            $router->get("/non-opulence-controller", "NonOpulenceController@showFoo");
        });
    }

    /**
     * Tests asserting that a view has a variable
     */
    public function testAssertViewHasVariable()
    {
        $this->testCase->route("GET", "/setvar");
        $this->testCase->assertViewHasVar("foo");
        $this->testCase->assertViewVarEquals("foo", "bar");
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
     * Tests that a logic exception is thrown if checking if a view has a variable when using a non-Opulence controller
     */
    public function testLogicExceptionCheckingIfViewHasVariableFromNonOpulenceController()
    {
        $this->setExpectedException(LogicException::class);
        $this->testCase->route("GET", "/non-opulence-controller");
        $this->testCase->assertViewHasVar("foo");
    }

    /**
     * Tests that a logic exception is thrown if getting a view variable when using a non-Opulence controller
     */
    public function testLogicExceptionGettingViewVariableFromNonOpulenceController()
    {
        $this->setExpectedException(LogicException::class);
        $this->testCase->route("GET", "/non-opulence-controller");
        $this->testCase->assertViewVarEquals("bar", "foo");
    }
}
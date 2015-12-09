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
            $router->get("/json", "HttpApplicationTestController@showJson");
        });
    }

    /**
     * Tests asserting that a view has a variable
     */
    public function testAssertViewHasVariable()
    {
        $this->testCase->get("/setvar")->go();
        $this->assertSame($this->testCase, $this->testCase->assertViewHasVar("foo"));
        $this->assertSame($this->testCase, $this->testCase->assertViewVarEquals("foo", "bar"));
    }

    /**
     * Tests asserting that a path redirects to another
     */
    public function testAssertingRedirect()
    {
        $this->testCase->get("/redirect")->go();
        $this->assertSame($this->testCase, $this->testCase->assertRedirectsTo("/redirectedPath"));
    }

    /**
     * Tests asserting that a response has certain content
     */
    public function testAssertingResponseHasContent()
    {
        $this->testCase->get("/foobar")->go();
        $this->assertSame($this->testCase, $this->testCase->assertResponseContentEquals("FooBar"));
    }

    /**
     * Tests asserting that a response has a certain cookie
     */
    public function testAssertingResponseHasCookie()
    {
        $this->testCase->get("/cookie")->go();
        $this->assertSame($this->testCase, $this->testCase->assertResponseHasCookie("foo"));
        $this->assertSame($this->testCase, $this->testCase->assertResponseCookieValueEquals("foo", "bar"));
    }

    /**
     * Tests asserting that a response has a certain header
     */
    public function testAssertingResponseHasHeader()
    {
        $this->testCase->get("/header")->go();
        $this->assertSame($this->testCase, $this->testCase->assertResponseHasHeader("foo"));
        $this->assertSame($this->testCase, $this->testCase->assertResponseHeaderEquals("foo", "bar"));
    }

    /**
     * Tests asserting that a response has status code
     */
    public function testAssertingResponseHasStatusCode()
    {
        $this->testCase->get("/badgateway")->go();
        $this->assertSame(
            $this->testCase,
            $this->testCase->assertResponseStatusCodeEquals(ResponseHeaders::HTTP_BAD_GATEWAY)
        );
    }

    /**
     * Tests asserting that a response is an internal server error
     */
    public function testAssertingResponseIsInternalServerError()
    {
        $this->testCase->get("/ise")->go();
        $this->assertSame($this->testCase, $this->testCase->assertResponseIsInternalServerError());
    }

    /**
     * Tests asserting that a response is not found
     */
    public function testAssertingResponseIsNotFound()
    {
        $this->testCase->get("/notfound")->go();
        $this->assertSame($this->testCase, $this->testCase->assertResponseIsNotFound());
    }

    /**
     * Tests asserting that a response is OK
     */
    public function testAssertingResponseIsOK()
    {
        $this->testCase->get("/ok")->go();
        $this->assertSame($this->testCase, $this->testCase->assertResponseIsOK());
    }

    /**
     * Tests asserting that a response is unauthorized
     */
    public function testAssertingResponseIsUnauthorized()
    {
        $this->testCase->get("/unauthorized")->go();
        $this->assertSame($this->testCase, $this->testCase->assertResponseIsUnauthorized());
    }

    /**
     * Tests asserting response JSON contains
     */
    public function testAssertingResponseJsonContains()
    {
        $this->testCase->get("/json")->go();
        $this->assertSame($this->testCase, $this->testCase->assertResponseJsonContains(["foo" => "bar"]));
        $this->assertSame($this->testCase, $this->testCase->assertResponseJsonContains(["baz" => "blah"]));
    }

    /**
     * Tests asserting response JSON equals
     */
    public function testAssertingResponseJsonEquals()
    {
        $this->testCase->get("/json")->go();
        $this->assertSame($this->testCase,
            $this->testCase->assertResponseJsonEquals(["foo" => "bar", "baz" => "blah"]));
    }

    /**
     * Tests that assertions called from request builder are forwarded to test case
     */
    public function testAssertionsCalledFromRequestBuilderAreForwardedToTestCase()
    {
        $this->assertSame(
            $this->testCase,
            $this->testCase->get("/foobar")
                ->assertResponseContentEquals("FooBar")
        );
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
        $this->testCase->get("/non-opulence-controller")->go();
        $this->assertSame($this->testCase, $this->testCase->assertViewHasVar("foo"));
    }

    /**
     * Tests that a logic exception is thrown if getting a view variable when using a non-Opulence controller
     */
    public function testLogicExceptionGettingViewVariableFromNonOpulenceController()
    {
        $this->setExpectedException(LogicException::class);
        $this->testCase->get("/non-opulence-controller")->go();
        $this->assertSame($this->testCase, $this->testCase->assertViewVarEquals("bar", "foo"));
    }

    /**
     * Tests that not chaining assertions to the request builder still works
     */
    public function testNotChainingAssertionsToRequestBuilderStillWorks()
    {
        $this->testCase->get("/ok");
        $this->testCase->assertResponseIsOK();
    }

    /**
     * Tests that verbs return request builders
     */
    public function testVerbsReturnRequestBuilders()
    {
        $this->assertInstanceOf(RequestBuilder::class, $this->testCase->delete());
        $this->assertInstanceOf(RequestBuilder::class, $this->testCase->get());
        $this->assertInstanceOf(RequestBuilder::class, $this->testCase->head());
        $this->assertInstanceOf(RequestBuilder::class, $this->testCase->options());
        $this->assertInstanceOf(RequestBuilder::class, $this->testCase->patch());
        $this->assertInstanceOf(RequestBuilder::class, $this->testCase->post());
        $this->assertInstanceOf(RequestBuilder::class, $this->testCase->put());
    }
}
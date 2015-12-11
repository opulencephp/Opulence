<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Testing\PhpUnit\Http;

use InvalidArgumentException;
use Opulence\Http\Requests\Request;
use Opulence\Http\Requests\UploadedFile;

/**
 * Tests the request builder
 */
class RequestBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var ApplicationTestCase|\PHPUnit_Framework_MockObject_MockObject testCase The test case to use in tests */
    private $testCase = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->testCase = $this->getMock(ApplicationTestCase::class);
    }

    /**
     * Tests building an HTTPS request
     */
    public function testBuildingHttpsRequest()
    {
        $requestBuilder = new RequestBuilder($this->testCase, "GET");
        $requestBuilder->from("https://foo.com/bar");
        $request = Request::createFromUrl("https://foo.com/bar", "GET");
        $this->testCase->expects($this->once())
            ->method("route")
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests building a JSON request
     */
    public function testBuildingJsonRequest()
    {
        $jsonArray = ["bar" => "baz"];
        $requestBuilder = new RequestBuilder($this->testCase, "GET", "/foo");
        $this->assertSame($requestBuilder, $requestBuilder->withJson($jsonArray));
        $request = Request::createFromUrl(
            "/foo",
            "GET",
            [],
            [],
            ["CONTENT_TYPE" => "application/json", "CONTENT_LENGTH" => mb_strlen(json_encode($jsonArray), "8bit")],
            [],
            [],
            json_encode($jsonArray)
        );
        $this->testCase->expects($this->once())
            ->method("route")
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests building a request with content
     */
    public function testBuildingRequestWithContent()
    {
        $requestBuilder = new RequestBuilder($this->testCase, "GET", "/foo");
        $this->assertSame($requestBuilder, $requestBuilder->withRawBody("my-content"));
        $request = Request::createFromUrl("/foo", "GET", [], [], [], [], [], "my-content");
        $this->testCase->expects($this->once())
            ->method("route")
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests building a request with cookies
     */
    public function testBuildingRequestWithCookies()
    {
        $requestBuilder = new RequestBuilder($this->testCase, "GET", "/foo");
        $cookies = ["cooke-name" => "cookie-val"];
        $this->assertSame($requestBuilder, $requestBuilder->withCookies($cookies));
        $request = Request::createFromUrl("/foo", "GET", [], $cookies);
        $this->testCase->expects($this->once())
            ->method("route")
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests building a request with env vars
     */
    public function testBuildingRequestWithEnvVars()
    {
        $requestBuilder = new RequestBuilder($this->testCase, "GET", "/foo");
        $env = ["env-name" => "env-val"];
        $this->assertSame($requestBuilder, $requestBuilder->withEnvironmentVars($env));
        $request = Request::createFromUrl("/foo", "GET", [], [], [], [], $env);
        $this->testCase->expects($this->once())
            ->method("route")
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests building a request with files
     */
    public function testBuildingRequestWithFiles()
    {
        $requestBuilder = new RequestBuilder($this->testCase, "GET", "/foo");
        $files = [new UploadedFile("/tmp/foo", "temp-filename", 123, "plain/text", UPLOAD_ERR_OK)];
        $this->assertSame(
            $requestBuilder,
            $requestBuilder->withFiles(
                $files
            )
        );
        $request = Request::createFromUrl("/foo", "GET", [], [], [], $files);
        $this->testCase->expects($this->once())
            ->method("route")
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests building a request with a port
     */
    public function testBuildingRequestWithPort()
    {
        $requestBuilder = new RequestBuilder($this->testCase, "GET");
        $requestBuilder->from("http://foo.com:8080/bar");
        $request = Request::createFromUrl("foo.com:8080/bar", "GET");
        $this->testCase->expects($this->once())
            ->method("route")
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests building a request with server vars
     */
    public function testBuildingRequestWithServerVars()
    {
        $requestBuilder = new RequestBuilder($this->testCase, "GET", "/foo");
        $serverVars = ["server-name" => "server-val"];
        $this->assertSame($requestBuilder, $requestBuilder->withServerVars($serverVars));
        $request = Request::createFromUrl("/foo", "GET", [], [], $serverVars);
        $this->testCase->expects($this->once())
            ->method("route")
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests that from() sets the Url in the request
     */
    public function testFromSetsUrlInRequest()
    {
        $requestBuilder = new RequestBuilder($this->testCase, "GET");
        $requestBuilder->from("/foo");
        $request = Request::createFromUrl("/foo", "GET");
        $this->testCase->expects($this->once())
            ->method("route")
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests that headers are prefixed with HTTP_
     */
    public function testHeadersArePrefixedWithHTTP()
    {
        $requestBuilder = new RequestBuilder($this->testCase, "GET", "/foo");
        $this->assertSame($requestBuilder, $requestBuilder->withHeaders(["FOO" => "bar"]));
        $request = Request::createFromUrl("/foo", "GET", [], [], ["HTTP_FOO" => "bar"]);
        $this->testCase->expects($this->once())
            ->method("route")
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests that not setting the method throws an exception
     */
    public function testNotSettingMethodThrowsException()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $requestBuilder = new RequestBuilder($this->testCase, "GET");
        $requestBuilder->go();
    }

    /**
     * Tests that not setting the URL throws an exception
     */
    public function testNotSettingUrlThrowsException()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $requestBuilder = new RequestBuilder($this->testCase, "GET");
        $requestBuilder->go();
    }

    /**
     * Tests that parameters in GET request are assigned to query
     */
    public function testParametersInGetRequestAreAssignedToQuery()
    {
        $requestBuilder = new RequestBuilder($this->testCase, "GET", "/foo");
        $parameters = ["name" => "val"];
        $this->assertSame($requestBuilder, $requestBuilder->withParameters($parameters));
        $request = Request::createFromUrl("/foo", "GET", $parameters);
        $this->testCase->expects($this->once())
            ->method("route")
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests that parameters in POST request are assigned to post
     */
    public function testParametersInPostRequestAreAssignedToPost()
    {
        $requestBuilder = new RequestBuilder($this->testCase, "POST", "/foo");
        $parameters = ["name" => "val"];
        $this->assertSame($requestBuilder, $requestBuilder->withParameters($parameters));
        $request = Request::createFromUrl("/foo", "POST", $parameters);
        $this->testCase->expects($this->once())
            ->method("route")
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests that to() sets the URL in the request
     */
    public function testToSetsUrlInRequest()
    {
        $requestBuilder = new RequestBuilder($this->testCase, "GET");
        $requestBuilder->to("/foo");
        $request = Request::createFromUrl("/foo", "GET");
        $this->testCase->expects($this->once())
            ->method("route")
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests that URL is set in constructor
     */
    public function testUrlSetInConstructor()
    {
        $requestBuilder = new RequestBuilder($this->testCase, "GET", "/foo");
        $request = Request::createFromUrl("/foo", "GET");
        $this->testCase->expects($this->once())
            ->method("route")
            ->with($request);
        $requestBuilder->go();
    }
}
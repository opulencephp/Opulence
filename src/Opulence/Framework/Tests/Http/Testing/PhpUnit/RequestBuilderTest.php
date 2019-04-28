<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Tests\Http\Testing\PhpUnit;

use InvalidArgumentException;
use Opulence\Framework\Http\Testing\PhpUnit\IntegrationTestCase;
use Opulence\Framework\Http\Testing\PhpUnit\RequestBuilder;
use Opulence\Http\Requests\Request;
use Opulence\Http\Requests\UploadedFile;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the request builder
 */
class RequestBuilderTest extends \PHPUnit\Framework\TestCase
{
    /** @var IntegrationTestCase|MockObject The integration test to use in tests */
    private $integrationTest = null;

    /**
     * Sets up the tests
     */
    protected function setUp() : void
    {
        $this->integrationTest = $this->createMock(IntegrationTestCase::class);
    }

    /**
     * Tests building an HTTPS request
     */
    public function testBuildingHttpsRequest() : void
    {
        $requestBuilder = new RequestBuilder($this->integrationTest, 'GET');
        $requestBuilder->from('https://foo.com/bar');
        $request = Request::createFromUrl('https://foo.com/bar', 'GET');
        $this->integrationTest->expects($this->once())
            ->method('route')
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests building a JSON request
     */
    public function testBuildingJsonRequest() : void
    {
        $jsonArray = ['bar' => 'baz'];
        $requestBuilder = new RequestBuilder($this->integrationTest, 'GET', '/foo');
        $this->assertSame($requestBuilder, $requestBuilder->withJson($jsonArray));
        $request = Request::createFromUrl(
            '/foo',
            'GET',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'CONTENT_LENGTH' => mb_strlen(json_encode($jsonArray), '8bit')],
            [],
            [],
            json_encode($jsonArray)
        );
        $this->integrationTest->expects($this->once())
            ->method('route')
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests building a request with content
     */
    public function testBuildingRequestWithContent() : void
    {
        $requestBuilder = new RequestBuilder($this->integrationTest, 'GET', '/foo');
        $this->assertSame($requestBuilder, $requestBuilder->withRawBody('my-content'));
        $request = Request::createFromUrl('/foo', 'GET', [], [], [], [], [], 'my-content');
        $this->integrationTest->expects($this->once())
            ->method('route')
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests building a request with cookies
     */
    public function testBuildingRequestWithCookies() : void
    {
        $requestBuilder = new RequestBuilder($this->integrationTest, 'GET', '/foo');
        $cookies = ['cooke-name' => 'cookie-val'];
        $this->assertSame($requestBuilder, $requestBuilder->withCookies($cookies));
        $request = Request::createFromUrl('/foo', 'GET', [], $cookies);
        $this->integrationTest->expects($this->once())
            ->method('route')
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests building a request with env vars
     */
    public function testBuildingRequestWithEnvVars() : void
    {
        $requestBuilder = new RequestBuilder($this->integrationTest, 'GET', '/foo');
        $env = ['env-name' => 'env-val'];
        $this->assertSame($requestBuilder, $requestBuilder->withEnvironmentVars($env));
        $request = Request::createFromUrl('/foo', 'GET', [], [], [], [], $env);
        $this->integrationTest->expects($this->once())
            ->method('route')
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests building a request with files
     */
    public function testBuildingRequestWithFiles() : void
    {
        $requestBuilder = new RequestBuilder($this->integrationTest, 'GET', '/foo');
        $files = [new UploadedFile('/tmp/foo', 'temp-filename', 123, 'plain/text', UPLOAD_ERR_OK)];
        $this->assertSame(
            $requestBuilder,
            $requestBuilder->withFiles(
                $files
            )
        );
        $request = Request::createFromUrl('/foo', 'GET', [], [], [], $files);
        $this->integrationTest->expects($this->once())
            ->method('route')
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests building a request with a port
     */
    public function testBuildingRequestWithPort() : void
    {
        $requestBuilder = new RequestBuilder($this->integrationTest, 'GET');
        $requestBuilder->from('http://foo.com:8080/bar');
        $request = Request::createFromUrl('foo.com:8080/bar', 'GET');
        $this->integrationTest->expects($this->once())
            ->method('route')
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests building a request with server vars
     */
    public function testBuildingRequestWithServerVars() : void
    {
        $requestBuilder = new RequestBuilder($this->integrationTest, 'GET', '/foo');
        $serverVars = ['server-name' => 'server-val'];
        $this->assertSame($requestBuilder, $requestBuilder->withServerVars($serverVars));
        $request = Request::createFromUrl('/foo', 'GET', [], [], $serverVars);
        $this->integrationTest->expects($this->once())
            ->method('route')
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests that from() sets the Url in the request
     */
    public function testFromSetsUrlInRequest() : void
    {
        $requestBuilder = new RequestBuilder($this->integrationTest, 'GET');
        $requestBuilder->from('/foo');
        $request = Request::createFromUrl('/foo', 'GET');
        $this->integrationTest->expects($this->once())
            ->method('route')
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests that headers are prefixed with HTTP_
     */
    public function testHeadersArePrefixedWithHTTP() : void
    {
        $requestBuilder = new RequestBuilder($this->integrationTest, 'GET', '/foo');
        $this->assertSame($requestBuilder, $requestBuilder->withHeaders(['FOO' => 'bar']));
        $request = Request::createFromUrl('/foo', 'GET', [], [], ['HTTP_FOO' => 'bar']);
        $this->integrationTest->expects($this->once())
            ->method('route')
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests that not setting the method throws an exception
     */
    public function testNotSettingMethodThrowsException() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $requestBuilder = new RequestBuilder($this->integrationTest, 'GET');
        $requestBuilder->go();
    }

    /**
     * Tests that not setting the URL throws an exception
     */
    public function testNotSettingUrlThrowsException() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $requestBuilder = new RequestBuilder($this->integrationTest, 'GET');
        $requestBuilder->go();
    }

    /**
     * Tests that parameters in GET request are assigned to query
     */
    public function testParametersInGetRequestAreAssignedToQuery() : void
    {
        $requestBuilder = new RequestBuilder($this->integrationTest, 'GET', '/foo');
        $parameters = ['name' => 'val'];
        $this->assertSame($requestBuilder, $requestBuilder->withParameters($parameters));
        $request = Request::createFromUrl('/foo', 'GET', $parameters);
        $this->integrationTest->expects($this->once())
            ->method('route')
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests that parameters in POST request are assigned to post
     */
    public function testParametersInPostRequestAreAssignedToPost() : void
    {
        $requestBuilder = new RequestBuilder($this->integrationTest, 'POST', '/foo');
        $parameters = ['name' => 'val'];
        $this->assertSame($requestBuilder, $requestBuilder->withParameters($parameters));
        $request = Request::createFromUrl('/foo', 'POST', $parameters);
        $this->integrationTest->expects($this->once())
            ->method('route')
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests that to() sets the URL in the request
     */
    public function testToSetsUrlInRequest() : void
    {
        $requestBuilder = new RequestBuilder($this->integrationTest, 'GET');
        $requestBuilder->to('/foo');
        $request = Request::createFromUrl('/foo', 'GET');
        $this->integrationTest->expects($this->once())
            ->method('route')
            ->with($request);
        $requestBuilder->go();
    }

    /**
     * Tests that URL is set in constructor
     */
    public function testUrlSetInConstructor() : void
    {
        $requestBuilder = new RequestBuilder($this->integrationTest, 'GET', '/foo');
        $request = Request::createFromUrl('/foo', 'GET');
        $this->integrationTest->expects($this->once())
            ->method('route')
            ->with($request);
        $requestBuilder->go();
    }
}

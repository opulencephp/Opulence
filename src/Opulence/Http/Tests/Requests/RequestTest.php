<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Http\Tests\Requests;

use InvalidArgumentException;
use Opulence\Http\Requests\Files;
use Opulence\Http\Requests\Request;
use Opulence\Http\Requests\RequestHeaders;
use Opulence\Http\Requests\RequestMethods;
use Opulence\Http\Requests\UploadedFile;
use Opulence\Http\Tests\Requests\Mocks\FormUrlEncodedRequest;
use Opulence\Http\Tests\Requests\Mocks\JsonRequest;
use RuntimeException;
use stdClass;

/**
 * Tests the HTTP request
 */
class RequestTest extends \PHPUnit\Framework\TestCase
{
    /** @var array A clone of the $_COOKIE array, which we can use to restore original values */
    private static $cookieClone = [];
    /** @var array A clone of the $_SERVER array, which we can use to restore original values */
    private static $serverClone = [];
    /** @var array A clone of the $_GET array, which we can use to restore original values */
    private static $getClone = [];
    /** @var array A clone of the $_POST array, which we can use to restore original values */
    private static $postClone = [];
    /** @var Request The request to use in tests */
    private $request = null;

    /**
     * Sets up all of the tests
     */
    public static function setUpBeforeClass() : void
    {
        self::$cookieClone = $_COOKIE;
        self::$serverClone = $_SERVER;
        self::$getClone = $_GET;
        self::$postClone = $_POST;
    }

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->request = new Request($_GET, $_POST, $_COOKIE, $_SERVER, $_FILES, $_ENV);
    }

    /**
     * Does some housekeeping before ending the tests
     */
    public function tearDown() : void
    {
        $_COOKIE = self::$cookieClone;
        $_SERVER = self::$serverClone;
        $_GET = self::$getClone;
        $_POST = self::$postClone;
    }

    /**
     * Tests automatically detecting the method
     */
    public function testAutomaticallyDetectingMethod() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $request = Request::createFromGlobals();
        $this->assertEquals('PUT', $request->getMethod());
    }

    /**
     * Tests the bug with PHP that writes CONTENT_ headers to HTTP_CONTENT_
     */
    public function testBugWithHttpContentHeaders() : void
    {
        $_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';
        $_SERVER['HTTP_CONTENT_LENGTH'] = 24;
        $request = Request::createFromGlobals();
        $this->assertEquals('application/json', $request->getHeaders()->get('CONTENT_TYPE'));
        $this->assertEquals(24, $request->getHeaders()->get('CONTENT_LENGTH'));

        // Try again by specifying server array
        $server = [];
        $server['HTTP_CONTENT_TYPE'] = 'application/json';
        $server['HTTP_CONTENT_LENGTH'] = 24;
        $request = Request::createFromGlobals(null, null, null, $server);
        $this->assertEquals('application/json', $request->getHeaders()->get('CONTENT_TYPE'));
        $this->assertEquals(24, $request->getHeaders()->get('CONTENT_LENGTH'));
    }

    /**
     * Tests uploaded files are converted to an array
     */
    public function testBuildingRequestWithFiles() : void
    {
        $files = [new UploadedFile('tmp', 'temp-filename', 123, 'plain/text', UPLOAD_ERR_OK)];
        $request = Request::createFromUrl('/foo', 'GET', [], [], [], $files);
        $this->assertEquals($files, $request->getFiles()->getAll());
    }

    /**
     * Tests checking that an unset DELETE variable is not set
     */
    public function testCheckingIfDeletePostVarIsNotSet() : void
    {
        $this->assertFalse($this->request->getDelete()->has('foo'));
    }

    /**
     * Tests checking that a set COOKIE is set
     */
    public function testCheckingIfSetCookieIsSet() : void
    {
        $_COOKIE['foo'] = 'bar';
        $this->request->getCookies()->exchangeArray($_COOKIE);
        $this->assertTrue($this->request->getCookies()->has('foo'));
    }

    /**
     * Tests checking that a set GET variable is set
     */
    public function testCheckingIfSetGetVarIsSet() : void
    {
        $_GET['foo'] = 'bar';
        $this->request->getQuery()->exchangeArray($_GET);
        $this->assertTrue($this->request->getQuery()->has('foo'));
    }

    /**
     * Tests checking that a set POST variable is set
     */
    public function testCheckingIfSetPostVarIsSet() : void
    {
        $_POST['foo'] = 'bar';
        $this->request->getPost()->exchangeArray($_POST);
        $this->assertTrue($this->request->getPost()->has('foo'));
    }

    /**
     * Tests checking that an unset COOKIE is set
     */
    public function testCheckingIfUnsetCookieIsSet() : void
    {
        $this->assertFalse($this->request->getCookies()->has('foo'));
    }

    /**
     * Tests checking that an unset GET variable is not set
     */
    public function testCheckingIfUnsetGetVarIsNotSet() : void
    {
        $this->assertFalse($this->request->getQuery()->has('foo'));
    }

    /**
     * Tests checking that an unset PATCH variable is not set
     */
    public function testCheckingIfUnsetPatchVarIsNotSet() : void
    {
        $this->assertFalse($this->request->getPatch()->has('foo'));
    }

    /**
     * Tests checking that an unset POST variable is not set
     */
    public function testCheckingIfUnsetPostVarIsNotSet() : void
    {
        $this->assertFalse($this->request->getPost()->has('foo'));
    }

    /**
     * Tests checking that an unset PUT variable is not set
     */
    public function testCheckingIfUnsetPutVarIsNotSet() : void
    {
        $this->assertFalse($this->request->getPut()->has('foo'));
    }

    /**
     * Tests that the client IP header is used when set
     */
    public function testClientIPHeaderUsedWhenSet() : void
    {
        Request::setTrustedHeaderName(RequestHeaders::CLIENT_IP, 'HTTP_CLIENT_IP');
        $_SERVER['HTTP_CLIENT_IP'] = '192.168.1.1';
        $request = Request::createFromGlobals();
        $this->assertEquals('192.168.1.1', $request->getClientIPAddress());
    }

    /**
     * Tests that the client port is used with a trusted proxy
     */
    public function testClientPortUsedToDeterminePortWithTrustedProxy() : void
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.1';
        $_SERVER['X-Forwarded-Port'] = 8080;
        Request::setTrustedProxies('192.168.1.1');
        Request::setTrustedHeaderName(RequestHeaders::CLIENT_PORT, 'X-Forwarded-Port');
        $request = Request::createFromGlobals();
        $this->assertEquals(8080, $request->getPort());
    }

    /**
     * Tests that the client proto is used with a trusted proxy
     */
    public function testClientProtoUsedToCheckIfSecureWithTrustedProxy() : void
    {
        // Try with HTTPS
        $_SERVER['REMOTE_ADDR'] = '192.168.1.1';
        $_SERVER['X-Forwarded-Proto'] = 'HTTPS';
        Request::setTrustedProxies('192.168.1.1');
        Request::setTrustedHeaderName(RequestHeaders::CLIENT_PROTO, 'X-Forwarded-Proto');
        $request = Request::createFromGlobals();
        $this->assertTrue($request->isSecure());

        // Try with SSL
        $_SERVER['X-Forwarded-Proto'] = 'ssl';
        $request = Request::createFromGlobals();
        $this->assertTrue($request->isSecure());

        // Try with "on"
        $_SERVER['X-Forwarded-Proto'] = 'on';
        $request = Request::createFromGlobals();
        $this->assertTrue($request->isSecure());

        // Try with HTTP
        $_SERVER['X-Forwarded-Proto'] = 'http';
        $request = Request::createFromGlobals();
        $this->assertFalse($request->isSecure());
    }

    /**
     * Tests that the client proto is used with a trusted proxy
     */
    public function testClientProtoUsedToDeterminePortWithTrustedProxy() : void
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.1';
        $_SERVER['X-Forwarded-Proto'] = 'https';
        Request::setTrustedProxies('192.168.1.1');
        Request::setTrustedHeaderName(RequestHeaders::CLIENT_PROTO, 'X-Forwarded-Proto');
        $request = Request::createFromGlobals();
        $this->assertEquals(443, $request->getPort());
    }

    /**
     * Tests cloning the credential
     */
    public function testCloning() : void
    {
        $clone = clone $this->request;
        $this->assertNotSame($clone, $this->request);
        $this->assertNotSame($clone->getCookies(), $this->request->getCookies());
        $this->assertNotSame($clone->getDelete(), $this->request->getDelete());
        $this->assertNotSame($clone->getEnv(), $this->request->getEnv());
        $this->assertNotSame($clone->getFiles(), $this->request->getFiles());
        $this->assertNotSame($clone->getHeaders(), $this->request->getHeaders());
        $this->assertNotSame($clone->getPatch(), $this->request->getPatch());
        $this->assertNotSame($clone->getPost(), $this->request->getPost());
        $this->assertNotSame($clone->getPut(), $this->request->getPut());
        $this->assertNotSame($clone->getQuery(), $this->request->getQuery());
        $this->assertNotSame($clone->getServer(), $this->request->getServer());
    }

    /**
     * Tests that the content type header is set for unsupported methods
     */
    public function testContentTypeSetForUnsupportedMethods() : void
    {
        $patchRequest = Request::createFromUrl('/url', 'PATCH');
        $this->assertEquals('application/x-www-form-urlencoded', $patchRequest->getServer()->get('CONTENT_TYPE'));
        $putRequest = Request::createFromUrl('/url', 'PUT');
        $this->assertEquals('application/x-www-form-urlencoded', $putRequest->getServer()->get('CONTENT_TYPE'));
        $deleteRequest = Request::createFromUrl('/url', 'DELETE');
        $this->assertEquals('application/x-www-form-urlencoded', $deleteRequest->getServer()->get('CONTENT_TYPE'));
    }

    /**
     * Tests the cookies are set from the URL
     */
    public function testCookiesAreSetFromUrl() : void
    {
        $vars = ['foo' => 'bar'];
        $request = Request::createFromUrl('/foo', 'GET', [], $vars);
        $this->assertEquals($vars, $request->getCookies()->getAll());
    }

    /**
     * Tests that checking that a correct path returns true
     */
    public function testCorrectPathReturnsTrue() : void
    {
        $_SERVER['REQUEST_URI'] = '/foo/bar/baz';
        $request = Request::createFromGlobals();
        $this->assertTrue($request->isPath('/foo/bar/baz'));
    }

    /**
     * Tests that checking that a correct regex path returns true
     */
    public function testCorrectRegexPathReturnsTrue() : void
    {
        $_SERVER['REQUEST_URI'] = '/foo/bar/baz';
        $request = Request::createFromGlobals();
        $this->assertTrue($request->isPath('.*/bar/baz', true));
    }

    /**
     * Tests that checking that a correct regex URL returns true
     */
    public function testCorrectRegexUrlReturnsTrue() : void
    {
        $_SERVER['SERVER_PORT'] = '80';
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['HTTP_HOST'] = 'foo.com';
        $_SERVER['REQUEST_URI'] = '/foo/bar/baz';
        $request = Request::createFromGlobals();
        $this->assertTrue($request->isUrl("http://[^\.]+\.com/foo/[^/]+/baz", true));
    }

    /**
     * Tests that checking that a correct URL returns true
     */
    public function testCorrectUrlReturnsTrue() : void
    {
        $_SERVER['SERVER_PORT'] = '80';
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['HTTP_HOST'] = 'foo.com';
        $_SERVER['REQUEST_URI'] = '/foo/bar/baz';
        $request = Request::createFromGlobals();
        $this->assertTrue($request->isUrl('http://foo.com/foo/bar/baz'));
    }

    /**
     * Tests creating from globals
     */
    public function testCreatingFromGlobals() : void
    {
        $requestFromConstructor = new Request($_GET, $_POST, $_COOKIE, $_SERVER, $_FILES, $_ENV);
        $this->assertEquals($requestFromConstructor, Request::createFromGlobals());
    }

    /**
     * Tests creating from globals with overridden globals and raw body
     */
    public function testCreatingFromGlobalsWithOverriddenGlobalsAndRawBody() : void
    {
        $get = ['get' => 'foo'];
        $post = ['post' => 'foo'];
        $cookie = ['cookie' => 'foo'];
        $server = ['server' => 'foo'];
        $files = [
            [
                'name' => 'foo.txt',
                'type' => 'text/plain',
                'tmp_name' => '/tmp/bar',
                'error' => UPLOAD_ERR_OK,
                'size' => 1024
            ]
        ];
        $env = ['env' => 'foo'];
        $rawBody = "It's not Rocket Appliances Julian";
        $requestFromConstructor = new Request($get, $post, $cookie, $server, $files, $env, $rawBody);
        $this->assertEquals(
            $requestFromConstructor,
            Request::createFromGlobals($get, $post, $cookie, $server, $files, $env, $rawBody)
        );
    }

    /**
     * Tests that a custom default value is returned when no input is found
     */
    public function testCustomDefaultIsReturnedWhenNoInputFound() : void
    {
        $request = Request::createFromGlobals();
        $this->assertEquals('bar', $request->getInput('foo', 'bar'));
    }

    /**
     * Tests that the default value is returned when getting non-existent input on a JSON request
     */
    public function testDefaultIsReturnedWhenGettingNonExistentInputOnJsonRequest() : void
    {
        $request = JsonRequest::createFromGlobals();
        $this->assertEquals('blah', $request->getInput('baz', 'blah'));
    }

    /**
     * Tests that the default server vars are overwritten from the URL
     */
    public function testDefaultServerVarsAreOverwrittenFromUrl() : void
    {
        $server = [
            'HTTP_ACCEPT' => 'the-accept',
            'HTTP_HOST' => 'the-host',
            'REMOTE_ADDR' => 'the-remote-addr',
            'SCRIPT_FILENAME' => 'script-filename',
            'SCRIPT_NAME' => 'script-name',
            'SERVER_NAME' => 'server-name',
            'SERVER_PROTOCOL' => 'server-protocol',
            'SERVER_PORT' => 8080,
            'QUERY_STRING' => ''
        ];
        $request = Request::createFromUrl('/foo', 'GET', [], [], $server);
        $allServerVars = array_merge($server, [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo'
        ]);
        $this->assertEquals($allServerVars, $request->getServer()->getAll());
    }

    /**
     * Tests that default server vars are set from the URL
     */
    public function testDefaultServerVarsSetFromUrl() : void
    {
        $server = [
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'HTTP_HOST' => 'localhost',
            'REMOTE_ADDR' => '127.0.01',
            'SCRIPT_FILENAME' => '',
            'SCRIPT_NAME' => '',
            'SERVER_NAME' => 'localhost',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'SERVER_PORT' => 80,
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo',
            'QUERY_STRING' => ''
        ];
        $request = Request::createFromUrl('/foo', 'GET');
        $this->assertEquals($server, $request->getServer()->getAll());
    }

    /**
     * Tests that the DELETE collection is returned on delete requests
     */
    public function testDeleteCollectionIsReturnedOnDeleteRequestWhenVarExists() : void
    {
        $methods = ['PUT', 'PATCH', 'DELETE'];

        foreach ($methods as $method) {
            $_SERVER['REQUEST_METHOD'] = $method;
            $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
            $request = FormUrlEncodedRequest::createFromGlobals();
            $this->assertEquals('bar', $request->getInput('foo'));
        }
    }

    /**
     * Tests that an empty string is returned when no referrer nor previous URL is set
     */
    public function testEmptyStringWhenNoReferrerNorPreviousUrlIsSet() : void
    {
        $request = Request::createFromGlobals();
        $this->assertEquals('', $request->getPreviousUrl());
    }

    /**
     * Tests the env vars are set from the URL
     */
    public function testEnvIsSetFromUrl() : void
    {
        $vars = ['foo' => 'bar'];
        $request = Request::createFromUrl('/foo', 'GET', [], [], [], [], $vars);
        $this->assertEquals($vars, $request->getEnv()->getAll());
    }

    /**
     * Tests that an exception is thrown when using an untrusted proxy
     */
    public function testExceptionThrownWithUntrustedProxy() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $_SERVER['HTTP_HOST'] = '192.168.1.1, 192.168.1.2, 192.168.1.3';
        $request = Request::createFromGlobals();
        $request->getHost();
    }

    /**
     * Tests that the full URL is the same as the URL passed into the create method
     */
    public function testFullUrlIsSetFromUrl() : void
    {
        $url = 'https://foo.com:8080/bar/baz?dave=young';
        $request = Request::createFromUrl($url, 'GET');
        $this->assertEquals($url, $request->getFullUrl());
    }

    /**
     * Tests getting raw body when the body is set in the constructor
     */
    public function testGetRawBodyWithRequestSetInConstructor() : void
    {
        $testBody = "It's not Rocket Appliances Julian";
        $this->request = new Request($_GET, $_POST, $_COOKIE, $_SERVER, $_FILES, $_ENV, $testBody);
        $this->assertEquals($testBody, $this->request->getRawBody());
    }

    /**
     * Tests getting the connect method
     */
    public function testGettingConnectMethod() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'CONNECT';
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals(RequestMethods::CONNECT, $this->request->getServer()->get('REQUEST_METHOD'));
    }

    /**
     * Tests getting the cookies
     */
    public function testGettingCookies() : void
    {
        $this->assertSame($_COOKIE, $this->request->getCookies()->getAll());
    }

    /**
     * Tests getting data from put, patch, and delete methods
     */
    public function testGettingDataFromPutPatchDeleteMethods() : void
    {
        $methods = ['PUT', 'PATCH', 'DELETE'];

        foreach ($methods as $method) {
            $_SERVER['REQUEST_METHOD'] = $method;
            $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
            $request = FormUrlEncodedRequest::createFromGlobals();
            $this->assertEquals('foo=bar', $request->getRawBody());

            switch ($method) {
                case 'PUT':
                    $this->assertEquals('bar', $request->getPut()->get('foo'));
                    $this->assertNull($request->getPatch()->get('foo'));
                    $this->assertNull($request->getDelete()->get('foo'));

                    break;
                case 'PATCH':
                    $this->assertEquals('bar', $request->getPatch()->get('foo'));
                    $this->assertNull($request->getPut()->get('foo'));
                    $this->assertNull($request->getDelete()->get('foo'));

                    break;
                case 'DELETE':
                    $this->assertEquals('bar', $request->getDelete()->get('foo'));
                    $this->assertNull($request->getPut()->get('foo'));
                    $this->assertNull($request->getPatch()->get('foo'));

                    break;
            }
        }
    }

    /**
     * Tests getting data from put, patch, and delete methods from a faked (eg uses _method input) method
     */
    public function testGettingDataFromPutPatchDeleteMethodsForFakedMethodUsesRealMethodsCollection(): void
    {
        $methods = ['PUT', 'PATCH', 'DELETE'];

        foreach ($methods as $method) {
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
            $expectedCollection = ['foo' => 'bar', '_method' => $method];
            $request = Request::createFromGlobals(null, $expectedCollection);

            switch ($method) {
                case 'PUT':
                    $this->assertEquals('bar', $request->getPut()->get('foo'));
                    $this->assertNull($request->getPatch()->get('foo'));
                    $this->assertNull($request->getDelete()->get('foo'));

                    break;
                case 'PATCH':
                    $this->assertEquals('bar', $request->getPatch()->get('foo'));
                    $this->assertNull($request->getPut()->get('foo'));
                    $this->assertNull($request->getDelete()->get('foo'));

                    break;
                case 'DELETE':
                    $this->assertEquals('bar', $request->getDelete()->get('foo'));
                    $this->assertNull($request->getPut()->get('foo'));
                    $this->assertNull($request->getPatch()->get('foo'));

                    break;
            }
        }
    }

    /**
     * Tests getting the delete method
     */
    public function testGettingDeleteMethod() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals(RequestMethods::DELETE, $this->request->getServer()->get('REQUEST_METHOD'));
    }

    /**
     * Tests getting the environment variables
     */
    public function testGettingEnvironmentVariables() : void
    {
        $this->assertSame($_ENV, $this->request->getEnv()->getAll());
    }

    /**
     * Tests getting the files
     */
    public function testGettingFiles() : void
    {
        $files = [
            'foo' => [
                'tmp_name' => '/path/foo.txt',
                'name' => 'foo.txt',
                'type' => 'text/plain',
                'size' => 100,
                'error' => UPLOAD_ERR_EXTENSION
            ]
        ];
        $expectedValue = [
            'foo' => new UploadedFile(
                '/path/foo.txt',
                'foo.txt',
                100,
                'text/plain',
                UPLOAD_ERR_EXTENSION
            )
        ];
        $request = new Request([], [], [], [], $files, []);
        $this->assertInstanceOf(Files::class, $request->getFiles());
        $this->assertEquals($expectedValue, $request->getFiles()->getAll());
    }

    /**
     * Tests getting a forbidden host
     */
    public function testGettingForbiddenHost() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $_SERVER['HTTP_HOST'] = '!';
        $request = Request::createFromGlobals();
        $request->getHost();
    }

    /**
     * Tests getting the get method
     */
    public function testGettingGetMethod() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals(RequestMethods::GET, $this->request->getServer()->get('REQUEST_METHOD'));
    }

    /**
     * Tests getting the headers
     */
    public function testGettingHeaders() : void
    {
        $headerParameters = [];

        // Grab all of the server parameters that begin with "HTTP_"
        foreach ($this->request->getServer()->getAll() as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headerParameters[substr($key, 5)] = $value;
            }
        }

        $this->assertSame($headerParameters, $this->request->getHeaders()->getAll());
    }

    /**
     * Tests getting the host as it's set in Gogo inflight wifi
     */
    public function testGettingHostFromGogoInflightWifiHeaders() : void
    {
        $_SERVER['HTTP_X_FORWARDED_HOST'] = '172.19.131.152, 10.33.185.152';
        $_SERVER['HTTP_HOST'] = '123.456.789.101';
        $request = Request::createFromGlobals();
        $this->assertEquals('123.456.789.101', $request->getHost());
    }

    /**
     * Tests getting the host from the HTTP_HOST header
     */
    public function testGettingHostFromHttpHost() : void
    {
        $_SERVER['HTTP_HOST'] = 'foo.com';
        $request = Request::createFromGlobals();
        $this->assertEquals('foo.com', $request->getHost());
    }

    /**
     * Tests getting the host from the SERVER_ADDR header
     */
    public function testGettingHostFromServerAddr() : void
    {
        $_SERVER['SERVER_ADDR'] = 'foo.com';
        $request = Request::createFromGlobals();
        $this->assertEquals('foo.com', $request->getHost());
    }

    /**
     * Tests getting the host from the SERVER_NAME header
     */
    public function testGettingHostFromServerName() : void
    {
        $_SERVER['SERVER_NAME'] = 'foo.com';
        $request = Request::createFromGlobals();
        $this->assertEquals('foo.com', $request->getHost());
    }

    /**
     * Tests getting an HTTP URL
     */
    public function testGettingHttpUrl() : void
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = 80;
        $_SERVER['SERVER_NAME'] = 'foo.com';
        $_SERVER['REQUEST_URI'] = '/bar';
        $request = Request::createFromGlobals();
        $this->assertEquals('http://foo.com/bar', $request->getFullUrl());
    }

    /**
     * Tests getting an HTTPS URL
     */
    public function testGettingHttpsURL() : void
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = 443;
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_NAME'] = 'foo.com';
        $_SERVER['REQUEST_URI'] = '/bar';
        $request = Request::createFromGlobals();
        $this->assertEquals('https://foo.com/bar', $request->getFullUrl());
    }

    /**
     * Tests getting the JSON body
     */
    public function testGettingJsonBody() : void
    {
        $request = JsonRequest::createFromGlobals();
        $this->assertEquals(['foo' => 'bar'], $request->getJsonBody());
    }

    /**
     * Tests getting the JSON body when the content is not JSON
     */
    public function testGettingJsonBodyWhenContentIsNotJson() : void
    {
        $this->expectException(RuntimeException::class);
        $request = FormUrlEncodedRequest::createFromGlobals();
        $request->getJsonBody();
    }

    /**
     * Tests getting the method from the override header on a GET request
     */
    public function testGettingMethodFromOverrideHeaderOnGetRequest() : void
    {
        $_SERVER['REQUEST_METHOD'] = RequestMethods::GET;
        $_SERVER['X-HTTP-METHOD-OVERRIDE'] = RequestMethods::PUT;
        $request = Request::createFromGlobals();
        $this->assertEquals(RequestMethods::GET, $request->getMethod());
    }

    /**
     * Tests getting the method from the override header on a POST request
     */
    public function testGettingMethodFromOverrideHeaderOnPostRequest() : void
    {
        $_SERVER['REQUEST_METHOD'] = RequestMethods::POST;
        $_SERVER['X-HTTP-METHOD-OVERRIDE'] = RequestMethods::PUT;
        $request = Request::createFromGlobals();
        $this->assertEquals(RequestMethods::PUT, $request->getMethod());
    }

    /**
     * Tests getting the method when there is none set in the $_SERVER
     */
    public function testGettingMethodWhenNoneIsSet() : void
    {
        $this->request->getServer()->remove('REQUEST_METHOD');
        $this->assertNull($this->request->getServer()->get('REQUEST_METHOD'));
    }

    /**
     * Tests getting a non-standard port URL
     */
    public function testGettingNonStandardURL() : void
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = 8080;
        $_SERVER['SERVER_NAME'] = 'foo.com';
        $_SERVER['REQUEST_URI'] = '/bar';
        $request = Request::createFromGlobals();
        $this->assertEquals('http://foo.com:8080/bar', $request->getFullUrl());
    }

    /**
     * Tests getting the options method
     */
    public function testGettingOptionsMethod() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals(RequestMethods::OPTIONS, $this->request->getServer()->get('REQUEST_METHOD'));
    }

    /**
     * Tests getting the auth password
     */
    public function testGettingPassword() : void
    {
        $_SERVER['PHP_AUTH_PW'] = 'myPassword';
        $request = Request::createFromGlobals();
        $this->assertEquals('myPassword', $request->getPassword());
    }

    /**
     * Tests getting the patch method
     */
    public function testGettingPatchMethod() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'PATCH';
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals(RequestMethods::PATCH, $this->request->getServer()->get('REQUEST_METHOD'));
    }

    /**
     * Tests getting the path
     */
    public function testGettingPath() : void
    {
        $_SERVER['REQUEST_URI'] = '/foo/bar/baz';
        $request = Request::createFromGlobals();
        $this->assertEquals('/foo/bar/baz', $request->getPath());
    }

    /**
     * Tests getting the path when it is empty
     */
    public function testGettingPathWhenEmpty() : void
    {
        $_SERVER['REQUEST_URI'] = '';
        $request = Request::createFromGlobals();
        $this->assertEquals('/', $request->getPath());
    }

    /**
     * Tests getting the path when the URI has a query string
     */
    public function testGettingPathWithQueryStringInURI() : void
    {
        $_SERVER['REQUEST_URI'] = '/foo/bar/baz?a=1&b=2';
        $request = Request::createFromGlobals();
        $this->assertEquals('/foo/bar/baz', $request->getPath());
    }

    /**
     * Tests getting the post
     */
    public function testGettingPost() : void
    {
        $this->assertSame($_POST, $this->request->getPost()->getAll());
    }

    /**
     * Tests getting the post method
     */
    public function testGettingPostMethod() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals(RequestMethods::POST, $this->request->getServer()->get('REQUEST_METHOD'));
    }

    /**
     * Tests getting the purge method
     */
    public function testGettingPurgeMethod() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'PURGE';
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals(RequestMethods::PURGE, $this->request->getServer()->get('REQUEST_METHOD'));
    }

    /**
     * Tests getting the put method
     */
    public function testGettingPutMethod() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals(RequestMethods::PUT, $this->request->getServer()->get('REQUEST_METHOD'));
    }

    /**
     * Tests getting the query
     */
    public function testGettingQuery() : void
    {
        $this->assertSame($_GET, $this->request->getQuery()->getAll());
    }

    /**
     * Tests getting the query string
     */
    public function testGettingQueryString() : void
    {
        $queryString = 'foo=bar&blah=asdf';
        $_SERVER['QUERY_STRING'] = $queryString;
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals($queryString, $this->request->getServer()->get('QUERY_STRING'));
    }

    /**
     * Tests getting the raw body
     */
    public function testGettingRawBody() : void
    {
        $this->assertEmpty($this->request->getRawBody());
    }

    /**
     * Tests getting the request URI
     */
    public function testGettingRequestURI() : void
    {
        $_SERVER['REQUEST_URI'] = '/foo/bar';
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals('/foo/bar', $this->request->getServer()->get('REQUEST_URI'));
    }

    /**
     * Tests getting the request URI when none was set
     */
    public function testGettingRequestURIWhenNoneWasSet() : void
    {
        $this->request->getServer()->remove('REQUEST_URI');
        $this->assertEmpty($this->request->getServer()->get('REQUEST_URI'));
    }

    /**
     * Tests getting the server
     */
    public function testGettingServer() : void
    {
        $this->assertSame($_SERVER, $this->request->getServer()->getAll());
    }

    /**
     * Tests getting a set cookie
     */
    public function testGettingSetCookie() : void
    {
        $_COOKIE['foo'] = 'bar';
        $this->request->getCookies()->exchangeArray($_COOKIE);
        $this->assertEquals('bar', $this->request->getCookies()->get('foo'));
    }

    /**
     * Tests getting a set GET variable
     */
    public function testGettingSetGetVar() : void
    {
        $_GET['foo'] = 'bar';
        $this->request->getQuery()->exchangeArray($_GET);
        $this->assertEquals('bar', $this->request->getQuery()->get('foo'));
    }

    /**
     * Tests getting a set POST variable
     */
    public function testGettingSetPostVar() : void
    {
        $_POST['foo'] = 'bar';
        $this->request->getPost()->exchangeArray($_POST);
        $this->assertEquals('bar', $this->request->getPost()->get('foo'));
    }

    /**
     * Tests getting the trace method
     */
    public function testGettingTraceMethod() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'TRACE';
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals(RequestMethods::TRACE, $this->request->getServer()->get('REQUEST_METHOD'));
    }

    /**
     * Tests getting an unset cookie
     */
    public function testGettingUnsetCookie() : void
    {
        $this->assertNull($this->request->getCookies()->get('foo'));
    }

    /**
     * Tests getting an unset GET variable
     */
    public function testGettingUnsetGetVar() : void
    {
        $this->assertNull($this->request->getQuery()->get('foo'));
    }

    /**
     * Tests getting an unset POST variable
     */
    public function testGettingUnsetPostVar() : void
    {
        $this->assertNull($this->request->getPost()->get('foo'));
    }

    /**
     * Tests getting a URL with a query string
     */
    public function testGettingUrlWithQueryString() : void
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = 80;
        $_SERVER['SERVER_NAME'] = 'foo.com';
        $_SERVER['REQUEST_URI'] = '/bar?baz=blah';
        $request = Request::createFromGlobals();
        $this->assertEquals('http://foo.com/bar?baz=blah', $request->getFullUrl());
    }

    /**
     * Tests getting the auth user
     */
    public function testGettingUser() : void
    {
        $_SERVER['PHP_AUTH_USER'] = 'dave';
        $request = Request::createFromGlobals();
        $this->assertEquals('dave', $request->getUser());
    }

    /**
     * Tests that any headers without the HTTP_ prefix are set
     */
    public function testHeadersWithoutHttpPrefixAreSet() : void
    {
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $_SERVER['CONTENT_LENGTH'] = 24;
        $request = Request::createFromGlobals();
        $this->assertEquals('application/x-www-form-urlencoded', $request->getHeaders()->get('CONTENT_TYPE'));
        $this->assertEquals(24, $request->getHeaders()->get('CONTENT_LENGTH'));
    }

    /**
     * Tests that the host is set from the URL
     */
    public function testHostIsSetFromUrl() : void
    {
        $request = Request::createFromUrl('http://foo.com/bar', 'GET');
        $this->assertEquals('foo.com', $request->getHost());
        $request = Request::createFromUrl('http://foo.com:80/bar', 'GET');
        $this->assertEquals('foo.com', $request->getHost());
        $request = Request::createFromUrl('https://foo.com:443/bar', 'GET');
        $this->assertEquals('foo.com', $request->getHost());
        $request = Request::createFromUrl('http://foo.com:8080/bar', 'GET');
        $this->assertEquals('foo.com', $request->getHost());
    }

    /**
     * Tests that the host is set correctly when using a trusted proxy
     */
    public function testHostSetCorrectlyWithTrustedProxy() : void
    {
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'foo.com, bar.com';
        $_SERVER['REMOTE_ADDR'] = '192.168.2.1';
        Request::setTrustedProxies('192.168.2.1');
        $request = Request::createFromGlobals();
        $this->assertEquals('bar.com', $request->getHost());
    }

    /**
     * Tests checking if an insecure request is secure
     */
    public function testIfInsecureRequestIsSecure() : void
    {
        $this->assertFalse($this->request->isSecure());
        // Test for IIS
        $this->request->getServer()->set('HTTPS', 'off');
        $this->assertFalse($this->request->isSecure());
    }

    /**
     * Tests checking if a secure request is secure
     */
    public function testIfSecureRequestIsSecure() : void
    {
        // Test for IIS
        $this->request->getServer()->set('HTTPS', 'on');
        $this->assertTrue($this->request->isSecure());
    }

    /**
     * Tests that checking that an incorrect path returns false
     */
    public function testIncorrectPathReturnsFalse() : void
    {
        $_SERVER['REQUEST_URI'] = '/foo/bar';
        $request = Request::createFromGlobals();
        $this->assertFalse($request->isPath('/foo'));
        $this->assertFalse($request->isPath('/foo/ba[^r]'));
    }

    /**
     * Tests that checking that an incorrect URL returns true
     */
    public function testIncorrectUrlReturnsFalse() : void
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['HTTP_HOST'] = 'baz.com';
        $_SERVER['SERVER_PORT'] = 80;
        $_SERVER['REQUEST_URI'] = '/foo/bar/baz';
        $request = Request::createFromGlobals();
        $this->assertFalse($request->isUrl('http://foo.com/foo/bar/baz'));
        $this->assertFalse($request->isUrl("http://baz[\.]+\.com/foo/baz/.*", true));
    }

    /**
     * Tests passing an invalid object method
     */
    public function testInvalidObjectMethod() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $_SERVER['REQUEST_METHOD'] = new stdClass();
        Request::createFromGlobals();
    }

    /**
     * Tests passing an invalid string method
     */
    public function testInvalidStringMethod() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $_SERVER['REQUEST_METHOD'] = 'foo';
        Request::createFromGlobals();
    }

    /**
     * Tests checking if a request was made by AJAX
     */
    public function testIsAjax() : void
    {
        $this->request->getHeaders()->set('X_REQUESTED_WITH', 'XMLHttpRequest');
        $this->assertTrue($this->request->isAjax());
        $this->request->getHeaders()->remove('X_REQUESTED_WITH');
        $this->assertFalse($this->request->isAjax());
    }

    /**
     * Tests checking if a request is JSON
     */
    public function testIsJson() : void
    {
        $_SERVER['HTTP_CONTENT_TYPE'] = 'text/plain';
        $request = Request::createFromGlobals();
        $this->assertFalse($request->isJson());
        $_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';
        $request = Request::createFromGlobals();
        $this->assertTrue($request->isJson());
        $_SERVER['HTTP_CONTENT_TYPE'] = 'application/json; charset=utf-8';
        $request = Request::createFromGlobals();
        $this->assertTrue($request->isJson());
    }

    /**
     * Tests that a property from JSON is returned when getting input from a JSON request
     */
    public function testJsonIsReturnedWhenGettingInputFromJsonRequest() : void
    {
        $request = JsonRequest::createFromGlobals();
        $this->assertEquals('bar', $request->getInput('foo'));
    }

    /**
     * Tests that null is returned when no input is found
     */
    public function testNullIsReturnedWhenNoInputFound() : void
    {
        $request = Request::createFromGlobals();
        $this->assertNull($request->getInput('foo'));
    }

    /**
     * Tests that parameters in GET request are assigned to query
     */
    public function testParametersInGetRequestAreAssignedToQuery() : void
    {
        $parameters = ['name' => 'val'];
        $request = Request::createFromUrl('/foo', 'GET', $parameters);
        $this->assertEquals('val', $request->getQuery()->get('name'));
    }

    /**
     * Tests that parameters in POST request are assigned to post
     */
    public function testParametersInPostRequestAreAssignedTPost() : void
    {
        $parameters = ['name' => 'val'];
        $request = Request::createFromUrl('/foo', 'POST', $parameters);
        $this->assertEquals('val', $request->getPost()->get('name'));
    }

    /**
     * Tests passing the method in a GET request
     */
    public function testPassingMethodInGetRequest() : void
    {
        $_GET['_method'] = RequestMethods::PUT;
        $_SERVER['REQUEST_METHOD'] = RequestMethods::GET;
        $request = Request::createFromGlobals();
        $this->assertEquals(RequestMethods::GET, $request->getMethod());
    }

    /**
     * Tests passing the method in a POST request
     */
    public function testPassingMethodInPostRequest() : void
    {
        $_POST['_method'] = RequestMethods::PUT;
        $_SERVER['REQUEST_METHOD'] = RequestMethods::POST;
        $request = Request::createFromGlobals();
        $this->assertEquals(RequestMethods::PUT, $request->getMethod());
    }

    /**
     * Tests that the path is set from the URL
     */
    public function testPathSetFromUrl() : void
    {
        $request = Request::createFromUrl('http://foo.com/bar', 'GET');
        $this->assertEquals('/bar', $request->getPath());
    }

    /**
     * Tests that POST data is not overwritten on POST request
     */
    public function testPostDataNotOverwrittenOnPostRequest() : void
    {
        $_POST['foo'] = 'blahblahblah';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $request = FormUrlEncodedRequest::createFromGlobals();
        $this->assertEquals('blahblahblah', $request->getPost()->get('foo'));
    }

    /**
     * Tests that the post is returned when getting input
     */
    public function testPostIsReturnedFromInput() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['foo'] = 'bar';
        $request = Request::createFromGlobals();
        $this->assertEquals('bar', $request->getInput('foo'));
    }

    /**
     * Tests that the previous URL is just the referrer header when it wasn't specifically set
     */
    public function testPreviousUrlIsReferrerWhenItIsNotSet() : void
    {
        $_SERVER['HTTP_REFERER'] = 'http://foo.com';
        $request = Request::createFromGlobals();
        $this->assertEquals('http://foo.com', $request->getPreviousUrl());
        $this->assertEmpty($request->getPreviousUrl(false));
    }

    /**
     * Tests that the previous URL take precedence over the referrer header when it is set
     */
    public function testPreviousUrlTakesPrecedenceOverReferrerWhenSet() : void
    {
        $_SERVER['HTTP_REFERER'] = 'http://foo.com';
        $request = Request::createFromGlobals();
        $request->setPreviousUrl('http://bar.com');
        $this->assertEquals('http://bar.com', $request->getPreviousUrl());
    }

    /**
     * Tests that the query is given preference when getting input
     */
    public function testQueryIsGivenPreferenceWhenGettingInput() : void
    {
        $_GET['foo'] = 'bar';
        $_POST['foo'] = 'baz';
        $request = Request::createFromGlobals();
        $this->assertEquals('bar', $request->getInput('foo'));
    }

    /**
     * Tests that the query is returned when getting input
     */
    public function testQueryIsReturnedFromInput() : void
    {
        $_GET['foo'] = 'bar';
        $request = Request::createFromGlobals();
        $this->assertEquals('bar', $request->getInput('foo'));
    }

    /**
     * Tests that the query var is returned when no matching var exists in post data on post request
     */
    public function testQueryIsReturnedOnPostRequestWhenNoPostVarExists() : void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_GET['foo'] = 'bar';
        $request = Request::createFromGlobals();
        $this->assertEquals('bar', $request->getInput('foo'));
    }

    /**
     * Tests that the query string is set from the URL
     */
    public function testQueryStringSetFromUrl() : void
    {
        $request = Request::createFromUrl('http://foo.com/bar/?baz=blah&dave=young', 'GET');
        $expectedQuery = [
            'baz' => 'blah',
            'dave' => 'young'
        ];
        $this->assertEquals($expectedQuery, $request->getQuery()->getAll());
        $this->assertEquals('/bar/?baz=blah&dave=young', $request->getServer()->get('REQUEST_URI'));
        $this->assertEquals('baz=blah&dave=young', $request->getServer()->get('QUERY_STRING'));
    }

    /**
     * Tests that the query string vars in URL are overwritten by parameters
     */
    public function testQueryStringVarsInUrlAreOverwrittenByParameters() : void
    {
        $request = Request::createFromUrl('http://foo.com/bar/?baz=blah&dave=young', 'GET', ['baz' => 'yay']);
        $expectedQuery = [
            'baz' => 'yay',
            'dave' => 'young'
        ];
        $this->assertEquals($expectedQuery, $request->getQuery()->getAll());
        $this->assertEquals('/bar/?baz=yay&dave=young', $request->getServer()->get('REQUEST_URI'));
        $this->assertEquals('baz=yay&dave=young', $request->getServer()->get('QUERY_STRING'));
    }

    /**
     * Tests that the raw body is set from the Url
     */
    public function testRawBodySetFromUrl() : void
    {
        $request = Request::createFromUrl('/foo', 'GET', [], [], [], [], [], 'foo-bar-baz');
        $this->assertEquals('foo-bar-baz', $request->getRawBody());
    }

    /**
     * Tests that the remote address is used with a trusted proxy
     */
    public function testRemoteAddrUsedWithTrustedProxy() : void
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.1';
        Request::setTrustedProxies('192.168.1.1');
        $request = Request::createFromGlobals();
        $this->assertEquals('192.168.1.1', $request->getClientIPAddress());
    }

    /**
     * Tests that the scheme and port are set from the URL
     */
    public function testSchemeAndPortSetFromUrl() : void
    {
        $httpsRequest = Request::createFromUrl('https://foo.com/bar', 'GET');
        $this->assertEquals('on', $httpsRequest->getServer()->get('HTTPS'));
        $this->assertEquals(443, $httpsRequest->getServer()->get('SERVER_PORT'));
        $httpRequest = Request::createFromUrl('http://foo.com/bar', 'GET');
        $this->assertFalse($httpRequest->getServer()->has('HTTPS'));
        $this->assertEquals(80, $httpRequest->getServer()->get('SERVER_PORT'));
    }

    /**
     * Tests the server vars are set from the URL
     */
    public function testServerIsSetFromUrl() : void
    {
        $vars = ['foo' => 'bar'];
        $request = Request::createFromUrl('/foo', 'GET', [], [], $vars);
        $allVars = array_merge(
            [
                'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'HTTP_HOST' => 'localhost',
                'QUERY_STRING' => '',
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/foo',
                'REMOTE_ADDR' => '127.0.01',
                'SCRIPT_FILENAME' => '',
                'SCRIPT_NAME' => '',
                'SERVER_NAME' => 'localhost',
                'SERVER_PORT' => 80,
                'SERVER_PROTOCOL' => 'HTTP/1.1'
            ],
            $vars
        );
        $this->assertEquals($allVars, $request->getServer()->getAll());
    }

    /**
     * Tests setting the IP through the FORWARDED header
     *
     * @link https://tools.ietf.org/html/rfc7239
     */
    public function testSettingIPThroughForwardedHeader() : void
    {
        $ipData = [
            ['for="_gazonk"', '_gazonk'],
            ['for="[2001:db8:cafe::17]:4711"', '2001:db8:cafe::17'],
            ['for=192.0.2.60;proto=http;by=203.0.113.43', '192.0.2.60'],
            ['for=192.0.2.43, for=198.51.100.17', '198.51.100.17']
        ];
        Request::setTrustedHeaderName(RequestHeaders::FORWARDED, 'HTTP_FORWARDED');

        foreach ($ipData as $ipDatum) {
            $_SERVER['HTTP_FORWARDED'] = $ipDatum[0];
            $request = Request::createFromGlobals();
            $this->assertEquals($ipDatum[1], $request->getClientIPAddress());
        }
    }

    /**
     * Tests setting the method
     */
    public function testSettingMethod() : void
    {
        $this->request->setMethod('put');
        $this->assertEquals('PUT', $this->request->getMethod());
    }

    /**
     * Tests setting the path
     */
    public function testSettingPath() : void
    {
        $this->request->setPath('/foo');
        $this->assertEquals('/foo', $this->request->getPath());
    }

    /**
     * Tests that the port is removed from the host
     */
    public function testThatPortIsRemovedFromHost() : void
    {
        $_SERVER['HTTP_HOST'] = 'foo.com:8080';
        $request = Request::createFromGlobals();
        $this->assertEquals('foo.com', $request->getHost());
    }
}

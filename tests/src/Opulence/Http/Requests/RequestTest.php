<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Http\Requests;

use InvalidArgumentException;
use Opulence\Tests\Http\Requests\Mocks\FormUrlEncodedRequest;
use Opulence\Tests\Http\Requests\Mocks\JsonRequest;
use RuntimeException;
use stdClass;

/**
 * Tests the HTTP request
 */
class RequestTest extends \PHPUnit_Framework_TestCase
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
    public static function setUpBeforeClass()
    {
        self::$cookieClone = $_COOKIE;
        self::$serverClone = $_SERVER;
        self::$getClone = $_GET;
        self::$postClone = $_POST;
    }

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->request = new Request($_GET, $_POST, $_COOKIE, $_SERVER, $_FILES, $_ENV);
    }

    /**
     * Does some housekeeping before ending the tests
     */
    public function tearDown()
    {
        $_COOKIE = self::$cookieClone;
        $_SERVER = self::$serverClone;
        $_GET = self::$getClone;
        $_POST = self::$postClone;
    }

    /**
     * Tests automatically detecting the method
     */
    public function testAutomaticallyDetectingMethod()
    {
        $_SERVER["REQUEST_METHOD"] = "PUT";
        $request = Request::createFromGlobals();
        $this->assertEquals("PUT", $request->getMethod());
    }

    /**
     * Tests the bug with PHP that writes CONTENT_ headers to HTTP_CONTENT_
     */
    public function testBugWithHTTPContentHeaders()
    {
        $_SERVER["HTTP_CONTENT_TYPE"] = "application/json";
        $_SERVER["HTTP_CONTENT_LENGTH"] = 24;
        $request = Request::createFromGlobals();
        $this->assertEquals("application/json", $request->getHeaders()->get("CONTENT_TYPE"));
        $this->assertEquals(24, $request->getHeaders()->get("CONTENT_LENGTH"));
    }

    /**
     * Tests checking that an unset DELETE variable is not set
     */
    public function testCheckingIfDeletePostVarIsNotSet()
    {
        $this->assertFalse($this->request->getDelete()->has("foo"));
    }

    /**
     * Tests checking that a set COOKIE is set
     */
    public function testCheckingIfSetCookieIsSet()
    {
        $_COOKIE["foo"] = "bar";
        $this->request->getCookies()->exchangeArray($_COOKIE);
        $this->assertTrue($this->request->getCookies()->has("foo"));
    }

    /**
     * Tests checking that a set GET variable is set
     */
    public function testCheckingIfSetGetVarIsSet()
    {
        $_GET["foo"] = "bar";
        $this->request->getQuery()->exchangeArray($_GET);
        $this->assertTrue($this->request->getQuery()->has("foo"));
    }

    /**
     * Tests checking that a set POST variable is set
     */
    public function testCheckingIfSetPostVarIsSet()
    {
        $_POST["foo"] = "bar";
        $this->request->getPost()->exchangeArray($_POST);
        $this->assertTrue($this->request->getPost()->has("foo"));
    }

    /**
     * Tests checking that an unset COOKIE is set
     */
    public function testCheckingIfUnsetCookieIsSet()
    {
        $this->assertFalse($this->request->getCookies()->has("foo"));
    }

    /**
     * Tests checking that an unset GET variable is not set
     */
    public function testCheckingIfUnsetGetVarIsNotSet()
    {
        $this->assertFalse($this->request->getQuery()->has("foo"));
    }

    /**
     * Tests checking that an unset PATCH variable is not set
     */
    public function testCheckingIfUnsetPatchVarIsNotSet()
    {
        $this->assertFalse($this->request->getPatch()->has("foo"));
    }

    /**
     * Tests checking that an unset POST variable is not set
     */
    public function testCheckingIfUnsetPostVarIsNotSet()
    {
        $this->assertFalse($this->request->getPost()->has("foo"));
    }

    /**
     * Tests checking that an unset PUT variable is not set
     */
    public function testCheckingIfUnsetPutVarIsNotSet()
    {
        $this->assertFalse($this->request->getPut()->has("foo"));
    }

    /**
     * Tests cloning the credential
     */
    public function testCloning()
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
     * Tests that checking that a correct path returns true
     */
    public function testCorrectPathReturnsTrue()
    {
        $_SERVER["REQUEST_URI"] = "/foo/bar/baz";
        $request = Request::createFromGlobals();
        $this->assertTrue($request->isPath("/foo/bar/baz"));
    }

    /**
     * Tests that checking that a correct regex path returns true
     */
    public function testCorrectRegexPathReturnsTrue()
    {
        $_SERVER["REQUEST_URI"] = "/foo/bar/baz";
        $request = Request::createFromGlobals();
        $this->assertTrue($request->isPath(".*/bar/baz", true));
    }

    /**
     * Tests that checking that a correct regex URL returns true
     */
    public function testCorrectRegexUrlReturnsTrue()
    {
        $_SERVER["SERVER_PORT"] = "80";
        $_SERVER["SERVER_PROTOCOL"] = "HTTP/1.1";
        $_SERVER["HTTP_HOST"] = "foo.com";
        $_SERVER["REQUEST_URI"] = "/foo/bar/baz";
        $request = Request::createFromGlobals();
        $this->assertTrue($request->isUrl("http://[^\.]+\.com/foo/[^/]+/baz", true));
    }

    /**
     * Tests that checking that a correct URL returns true
     */
    public function testCorrectUrlReturnsTrue()
    {
        $_SERVER["SERVER_PORT"] = "80";
        $_SERVER["SERVER_PROTOCOL"] = "HTTP/1.1";
        $_SERVER["HTTP_HOST"] = "foo.com";
        $_SERVER["REQUEST_URI"] = "/foo/bar/baz";
        $request = Request::createFromGlobals();
        $this->assertTrue($request->isUrl("http://foo.com/foo/bar/baz"));
    }

    /**
     * Tests creating from globals
     */
    public function testCreatingFromGlobals()
    {
        $requestFromConstructor = new Request($_GET, $_POST, $_COOKIE, $_SERVER, $_FILES, $_ENV);
        $this->assertEquals($requestFromConstructor, Request::createFromGlobals());
    }

    /**
     * Tests that a custom default value is returned when no input is found
     */
    public function testCustomDefaultIsReturnedWhenNoInputFound()
    {
        $request = Request::createFromGlobals();
        $this->assertEquals("bar", $request->getInput("foo", "bar"));
    }

    /**
     * Tests that the default value is returned when getting non-existent input on a JSON request
     */
    public function testDefaultIsReturnedWhenGettingNonExistentInputOnJsonRequest()
    {
        $request = JsonRequest::createFromGlobals();
        $this->assertEquals("blah", $request->getInput("baz", "blah"));
    }

    /**
     * Tests getting the connect method
     */
    public function testGettingConnectMethod()
    {
        $_SERVER["REQUEST_METHOD"] = "CONNECT";
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals(Request::METHOD_CONNECT, $this->request->getServer()->get("REQUEST_METHOD"));
    }

    /**
     * Tests getting the cookies
     */
    public function testGettingCookies()
    {
        $this->assertSame($_COOKIE, $this->request->getCookies()->getAll());
    }

    /**
     * Tests getting data from put, patch, and delete methods
     */
    public function testGettingDataFromPutPatchDeleteMethods()
    {
        $methods = ["PUT", "PATCH", "DELETE"];

        foreach ($methods as $method) {
            $_SERVER["REQUEST_METHOD"] = $method;
            $_SERVER["CONTENT_TYPE"] = "application/x-www-form-urlencoded";
            $request = FormUrlEncodedRequest::createFromGlobals();
            $this->assertEquals("foo=bar", $request->getRawBody());

            switch ($method) {
                case "PUT":
                    $this->assertEquals("bar", $request->getPut()->get("foo"));
                    $this->assertNull($request->getPatch()->get("foo"));
                    $this->assertNull($request->getDelete()->get("foo"));

                    break;
                case "PATCH":
                    $this->assertEquals("bar", $request->getPatch()->get("foo"));
                    $this->assertNull($request->getPut()->get("foo"));
                    $this->assertNull($request->getDelete()->get("foo"));

                    break;
                case "DELETE":
                    $this->assertEquals("bar", $request->getDelete()->get("foo"));
                    $this->assertNull($request->getPut()->get("foo"));
                    $this->assertNull($request->getPatch()->get("foo"));

                    break;
            }
        }
    }

    /**
     * Tests getting the delete method
     */
    public function testGettingDeleteMethod()
    {
        $_SERVER["REQUEST_METHOD"] = "DELETE";
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals(Request::METHOD_DELETE, $this->request->getServer()->get("REQUEST_METHOD"));
    }

    /**
     * Tests getting the environment variables
     */
    public function testGettingEnvironmentVariables()
    {
        $this->assertSame($_ENV, $this->request->getEnv()->getAll());
    }

    /**
     * Tests getting the files
     */
    public function testGettingFiles()
    {
        $files = [
            "foo" => [
                "tmp_name" => "/path/foo.txt",
                "name" => "foo.txt",
                "type" => "text/plain",
                "size" => 100,
                "error" => UPLOAD_ERR_EXTENSION
            ]
        ];
        $expectedValue = [
            "foo" => new UploadedFile(
                "/path/foo.txt",
                "foo.txt",
                100,
                "text/plain",
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
    public function testGettingForbiddenHost()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $_SERVER["HTTP_HOST"] = "!";
        $request = Request::createFromGlobals();
        $request->getHost();
    }

    /**
     * Tests getting the get method
     */
    public function testGettingGetMethod()
    {
        $_SERVER["REQUEST_METHOD"] = "GET";
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals(Request::METHOD_GET, $this->request->getServer()->get("REQUEST_METHOD"));
    }

    /**
     * Tests getting the headers
     */
    public function testGettingHeaders()
    {
        $headerParameters = [];

        // Grab all of the server parameters that begin with "HTTP_"
        foreach ($this->request->getServer()->getAll() as $key => $value) {
            if (strpos($key, "HTTP_") === 0) {
                $headerParameters[substr($key, 5)] = $value;
            }
        }

        $this->assertSame($headerParameters, $this->request->getHeaders()->getAll());
    }

    /**
     * Tests getting the host from the HTTP_HOST header
     */
    public function testGettingHostFromHTTPHost()
    {
        $_SERVER["HTTP_HOST"] = "foo.com";
        $request = Request::createFromGlobals();
        $this->assertEquals("foo.com", $request->getHost());
    }

    /**
     * Tests getting the host from the SERVER_ADDR header
     */
    public function testGettingHostFromServerAddr()
    {
        $_SERVER["SERVER_ADDR"] = "foo.com";
        $request = Request::createFromGlobals();
        $this->assertEquals("foo.com", $request->getHost());
    }

    /**
     * Tests getting the host from the SERVER_NAME header
     */
    public function testGettingHostFromServerName()
    {
        $_SERVER["SERVER_NAME"] = "foo.com";
        $request = Request::createFromGlobals();
        $this->assertEquals("foo.com", $request->getHost());
    }

    /**
     * Tests getting the host from the X_FORWARDED_FOR header
     */
    public function testGettingHostFromXForwardedFor()
    {
        $_SERVER["HTTP_X_FORWARDED_FOR"] = "foo.com";
        $request = Request::createFromGlobals();
        $this->assertEquals("foo.com", $request->getHost());
    }

    /**
     * Tests getting an HTTP URL
     */
    public function testGettingHttpUrl()
    {
        $_SERVER["SERVER_PROTOCOL"] = "HTTP/1.1";
        $_SERVER["SERVER_PORT"] = 80;
        $_SERVER["SERVER_NAME"] = "foo.com";
        $_SERVER["REQUEST_URI"] = "/bar";
        $request = Request::createFromGlobals();
        $this->assertEquals("http://foo.com/bar", $request->getFullUrl());
    }

    /**
     * Tests getting an HTTPS URL
     */
    public function testGettingHttpsURL()
    {
        $_SERVER["SERVER_PROTOCOL"] = "HTTP/1.1";
        $_SERVER["SERVER_PORT"] = 443;
        $_SERVER["HTTPS"] = "on";
        $_SERVER["SERVER_NAME"] = "foo.com";
        $_SERVER["REQUEST_URI"] = "/bar";
        $request = Request::createFromGlobals();
        $this->assertEquals("https://foo.com/bar", $request->getFullUrl());
    }

    /**
     * Tests getting the IP address
     */
    public function testGettingIPAddress()
    {
        $defaultIPAddress = "120.138.20.36";
        $keys = [
            "HTTP_CLIENT_IP",
            "HTTP_X_FORWARDED_FOR",
            "HTTP_X_FORWARDED",
            "HTTP_X_CLUSTER_CLIENT_IP",
            "HTTP_FORWARDED_FOR",
            "HTTP_FORWARDED",
            "REMOTE_ADDR"
        ];

        // Delete all the keys that might hold an IP address
        foreach ($keys as $key) {
            unset($_SERVER[$key]);
        }

        // Set each key and try getting the IP address using it
        foreach ($keys as $key) {
            $_SERVER[$key] = $defaultIPAddress;
            $request = Request::createFromGlobals();
            $this->assertEquals($defaultIPAddress, $request->getIPAddress());
            unset($_SERVER[$key]);
        }
    }

    /**
     * Tests getting the JSON body
     */
    public function testGettingJsonBody()
    {
        $request = JsonRequest::createFromGlobals();
        $this->assertEquals(["foo" => "bar"], $request->getJsonBody());
    }

    /**
     * Tests getting the JSON body when the content is not JSON
     */
    public function testGettingJsonBodyWhenContentIsNotJson()
    {
        $this->setExpectedException(RuntimeException::class);
        $request = FormUrlEncodedRequest::createFromGlobals();
        $request->getJsonBody();
    }

    /**
     * Tests getting the method from the override header on a GET request
     */
    public function testGettingMethodFromOverrideHeaderOnGetRequest()
    {
        $_SERVER["REQUEST_METHOD"] = Request::METHOD_GET;
        $_SERVER["X-HTTP-METHOD-OVERRIDE"] = Request::METHOD_PUT;
        $request = Request::createFromGlobals();
        $this->assertEquals(Request::METHOD_GET, $request->getMethod());
    }

    /**
     * Tests getting the method from the override header on a POST request
     */
    public function testGettingMethodFromOverrideHeaderOnPostRequest()
    {
        $_SERVER["REQUEST_METHOD"] = Request::METHOD_POST;
        $_SERVER["X-HTTP-METHOD-OVERRIDE"] = Request::METHOD_PUT;
        $request = Request::createFromGlobals();
        $this->assertEquals(Request::METHOD_PUT, $request->getMethod());
    }

    /**
     * Tests getting the method when there is none set in the $_SERVER
     */
    public function testGettingMethodWhenNoneIsSet()
    {
        $this->request->getServer()->remove("REQUEST_METHOD");
        $this->assertNull($this->request->getServer()->get("REQUEST_METHOD"));
    }

    /**
     * Tests getting a non-standard port URL
     */
    public function testGettingNonStandardURL()
    {
        $_SERVER["SERVER_PROTOCOL"] = "HTTP/1.1";
        $_SERVER["SERVER_PORT"] = 8080;
        $_SERVER["SERVER_NAME"] = "foo.com";
        $_SERVER["REQUEST_URI"] = "/bar";
        $request = Request::createFromGlobals();
        $this->assertEquals("http://foo.com:8080/bar", $request->getFullUrl());
    }

    /**
     * Tests getting the options method
     */
    public function testGettingOptionsMethod()
    {
        $_SERVER["REQUEST_METHOD"] = "OPTIONS";
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals(Request::METHOD_OPTIONS, $this->request->getServer()->get("REQUEST_METHOD"));
    }

    /**
     * Tests getting the auth password
     */
    public function testGettingPassword()
    {
        $_SERVER["PHP_AUTH_PW"] = "myPassword";
        $request = Request::createFromGlobals();
        $this->assertEquals("myPassword", $request->getPassword());
    }

    /**
     * Tests getting the patch method
     */
    public function testGettingPatchMethod()
    {
        $_SERVER["REQUEST_METHOD"] = "PATCH";
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals(Request::METHOD_PATCH, $this->request->getServer()->get("REQUEST_METHOD"));
    }

    /**
     * Tests getting the path
     */
    public function testGettingPath()
    {
        $_SERVER["REQUEST_URI"] = "/foo/bar/baz";
        $request = Request::createFromGlobals();
        $this->assertEquals("/foo/bar/baz", $request->getPath());
    }

    /**
     * Tests getting the path when it is empty
     */
    public function testGettingPathWhenEmpty()
    {
        $_SERVER["REQUEST_URI"] = "";
        $request = Request::createFromGlobals();
        $this->assertEquals("/", $request->getPath());
    }

    /**
     * Tests getting the path when the URI has a query string
     */
    public function testGettingPathWithQueryStringInURI()
    {
        $_SERVER["REQUEST_URI"] = "/foo/bar/baz?a=1&b=2";
        $request = Request::createFromGlobals();
        $this->assertEquals("/foo/bar/baz", $request->getPath());
    }

    /**
     * Tests getting the post
     */
    public function testGettingPost()
    {
        $this->assertSame($_POST, $this->request->getPost()->getAll());
    }

    /**
     * Tests getting the post method
     */
    public function testGettingPostMethod()
    {
        $_SERVER["REQUEST_METHOD"] = "POST";
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals(Request::METHOD_POST, $this->request->getServer()->get("REQUEST_METHOD"));
    }

    /**
     * Tests getting the purge method
     */
    public function testGettingPurgeMethod()
    {
        $_SERVER["REQUEST_METHOD"] = "PURGE";
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals(Request::METHOD_PURGE, $this->request->getServer()->get("REQUEST_METHOD"));
    }

    /**
     * Tests getting the put method
     */
    public function testGettingPutMethod()
    {
        $_SERVER["REQUEST_METHOD"] = "PUT";
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals(Request::METHOD_PUT, $this->request->getServer()->get("REQUEST_METHOD"));
    }

    /**
     * Tests getting the query
     */
    public function testGettingQuery()
    {
        $this->assertSame($_GET, $this->request->getQuery()->getAll());
    }

    /**
     * Tests getting the query string
     */
    public function testGettingQueryString()
    {
        $queryString = "foo=bar&blah=asdf";
        $_SERVER["QUERY_STRING"] = $queryString;
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals($queryString, $this->request->getServer()->get("QUERY_STRING"));
    }

    /**
     * Tests getting the raw body
     */
    public function testGettingRawBody()
    {
        $this->assertEmpty($this->request->getRawBody());
    }

    /**
     * Tests getting the request URI
     */
    public function testGettingRequestURI()
    {
        $_SERVER["REQUEST_URI"] = "/foo/bar";
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals("/foo/bar", $this->request->getServer()->get("REQUEST_URI"));
    }

    /**
     * Tests getting the request URI when none was set
     */
    public function testGettingRequestURIWhenNoneWasSet()
    {
        $this->request->getServer()->remove("REQUEST_URI");
        $this->assertEmpty($this->request->getServer()->get("REQUEST_URI"));
    }

    /**
     * Tests getting the server
     */
    public function testGettingServer()
    {
        $this->assertSame($_SERVER, $this->request->getServer()->getAll());
    }

    /**
     * Tests getting a set cookie
     */
    public function testGettingSetCookie()
    {
        $_COOKIE["foo"] = "bar";
        $this->request->getCookies()->exchangeArray($_COOKIE);
        $this->assertEquals("bar", $this->request->getCookies()->get("foo"));
    }

    /**
     * Tests getting a set GET variable
     */
    public function testGettingSetGetVar()
    {
        $_GET["foo"] = "bar";
        $this->request->getQuery()->exchangeArray($_GET);
        $this->assertEquals("bar", $this->request->getQuery()->get("foo"));
    }

    /**
     * Tests getting a set POST variable
     */
    public function testGettingSetPostVar()
    {
        $_POST["foo"] = "bar";
        $this->request->getPost()->exchangeArray($_POST);
        $this->assertEquals("bar", $this->request->getPost()->get("foo"));
    }

    /**
     * Tests getting the trace method
     */
    public function testGettingTraceMethod()
    {
        $_SERVER["REQUEST_METHOD"] = "TRACE";
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals(Request::METHOD_TRACE, $this->request->getServer()->get("REQUEST_METHOD"));
    }

    /**
     * Tests getting an unset cookie
     */
    public function testGettingUnsetCookie()
    {
        $this->assertNull($this->request->getCookies()->get("foo"));
    }

    /**
     * Tests getting an unset GET variable
     */
    public function testGettingUnsetGetVar()
    {
        $this->assertNull($this->request->getQuery()->get("foo"));
    }

    /**
     * Tests getting an unset POST variable
     */
    public function testGettingUnsetPostVar()
    {
        $this->assertNull($this->request->getPost()->get("foo"));
    }

    /**
     * Tests getting a URL with a query string
     */
    public function testGettingUrlWithQueryString()
    {
        $_SERVER["SERVER_PROTOCOL"] = "HTTP/1.1";
        $_SERVER["SERVER_PORT"] = 80;
        $_SERVER["SERVER_NAME"] = "foo.com";
        $_SERVER["REQUEST_URI"] = "/bar?baz=blah";
        $request = Request::createFromGlobals();
        $this->assertEquals("http://foo.com/bar?baz=blah", $request->getFullUrl());
    }

    /**
     * Tests getting the auth user
     */
    public function testGettingUser()
    {
        $_SERVER["PHP_AUTH_USER"] = "dave";
        $request = Request::createFromGlobals();
        $this->assertEquals("dave", $request->getUser());
    }

    /**
     * Tests that any headers without the HTTP_ prefix are set
     */
    public function testHeadersWithoutHTTPPrefixAreSet()
    {
        $_SERVER["CONTENT_TYPE"] = "application/x-www-form-urlencoded";
        $_SERVER["CONTENT_LENGTH"] = 24;
        $request = Request::createFromGlobals();
        $this->assertEquals("application/x-www-form-urlencoded", $request->getHeaders()->get("CONTENT_TYPE"));
        $this->assertEquals(24, $request->getHeaders()->get("CONTENT_LENGTH"));
    }

    /**
     * Tests checking if an insecure request is secure
     */
    public function testIfInsecureRequestIsSecure()
    {
        $this->assertFalse($this->request->isSecure());
        // Test for IIS
        $this->request->getServer()->set("HTTPS", "off");
        $this->assertFalse($this->request->isSecure());
    }

    /**
     * Tests checking if a secure request is secure
     */
    public function testIfSecureRequestIsSecure()
    {
        // Test for IIS
        $this->request->getServer()->set("HTTPS", "on");
        $this->assertTrue($this->request->isSecure());
    }

    /**
     * Tests that checking that an incorrect path returns false
     */
    public function testIncorrectPathReturnsFalse()
    {
        $_SERVER["REQUEST_URI"] = "/foo/bar";
        $request = Request::createFromGlobals();
        $this->assertFalse($request->isPath("/foo"));
        $this->assertFalse($request->isPath("/foo/ba[^r]"));
    }

    /**
     * Tests that checking that an incorrect URL returns true
     */
    public function testIncorrectUrlReturnsFalse()
    {
        $_SERVER["SERVER_PROTOCOL"] = "HTTP/1.1";
        $_SERVER["HTTP_HOST"] = "baz.com";
        $_SERVER["REQUEST_URI"] = "/foo/bar/baz";
        $request = Request::createFromGlobals();
        $this->assertFalse($request->isUrl("http://foo.com/foo/bar/baz"));
        $this->assertFalse($request->isUrl("http://baz[\.]+\.com/foo/baz/.*", true));
    }

    /**
     * Tests passing an invalid object method
     */
    public function testInvalidObjectMethod()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $_SERVER["REQUEST_METHOD"] = new stdClass();
        Request::createFromGlobals();
    }

    /**
     * Tests passing an invalid string method
     */
    public function testInvalidStringMethod()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $_SERVER["REQUEST_METHOD"] = "foo";
        Request::createFromGlobals();
    }

    /**
     * Tests checking if a request was made by AJAX
     */
    public function testIsAJAX()
    {
        $this->request->getHeaders()->set("X_REQUESTED_WITH", "XMLHttpRequest");
        $this->assertTrue($this->request->isAjax());
        $this->request->getHeaders()->remove("X_REQUESTED_WITH");
        $this->assertFalse($this->request->isAjax());
    }

    /**
     * Tests checking if a request is JSON
     */
    public function testIsJson()
    {
        $_SERVER["HTTP_CONTENT_TYPE"] = "text/plain";
        $request = Request::createFromGlobals();
        $this->assertFalse($request->isJson());
        $_SERVER["HTTP_CONTENT_TYPE"] = "application/json";
        $request = Request::createFromGlobals();
        $this->assertTrue($request->isJson());
    }

    /**
     * Tests that a property from JSON is returned when getting input from a JSON request
     */
    public function testJsonIsReturnedWhenGettingInputFromJsonRequest()
    {
        $request = JsonRequest::createFromGlobals();
        $this->assertEquals("bar", $request->getInput("foo"));
    }

    /**
     * Tests that null is returned when no input is found
     */
    public function testNullIsReturnedWhenNoInputFound()
    {
        $request = Request::createFromGlobals();
        $this->assertNull($request->getInput("foo"));
    }

    /**
     * Tests passing the method in a GET request
     */
    public function testPassingMethodInGetRequest()
    {
        $_GET["_method"] = Request::METHOD_PUT;
        $_SERVER["REQUEST_METHOD"] = Request::METHOD_GET;
        $request = Request::createFromGlobals();
        $this->assertEquals(Request::METHOD_GET, $request->getMethod());
    }

    /**
     * Tests passing the method in a POST request
     */
    public function testPassingMethodInPostRequest()
    {
        $_POST["_method"] = Request::METHOD_PUT;
        $_SERVER["REQUEST_METHOD"] = Request::METHOD_POST;
        $request = Request::createFromGlobals();
        $this->assertEquals(Request::METHOD_PUT, $request->getMethod());
    }

    /**
     * Tests that POST data is not overwritten on POST request
     */
    public function testPostDataNotOverwrittenOnPostRequest()
    {
        $_POST["foo"] = "blahblahblah";
        $_SERVER["REQUEST_METHOD"] = "POST";
        $_SERVER["CONTENT_TYPE"] = "application/x-www-form-urlencoded";
        $request = FormUrlEncodedRequest::createFromGlobals();
        $this->assertEquals("blahblahblah", $request->getPost()->get("foo"));
    }

    /**
     * Tests that the post is returned when getting input
     */
    public function testPostIsReturnedFromInput()
    {
        $_SERVER["REQUEST_METHOD"] = "POST";
        $_POST["foo"] = "bar";
        $request = Request::createFromGlobals();
        $this->assertEquals("bar", $request->getInput("foo"));
    }

    /**
     * Tests that the previous URL is just the referrer header when it wasn't specifically set
     */
    public function testPreviousURLIsReferrerWhenItIsNotSet()
    {
        $_SERVER["HTTP_REFERER"] = "http://foo.com";
        $request = Request::createFromGlobals();
        $this->assertEquals("http://foo.com", $request->getPreviousUrl());
        $this->assertEmpty($request->getPreviousUrl(false));
    }

    /**
     * Tests that the previous URL take precedence over the referrer header when it is set
     */
    public function testPreviousUrlTakesPrecedenceOverReferrerWhenSet()
    {
        $_SERVER["HTTP_REFERER"] = "http://foo.com";
        $request = Request::createFromGlobals();
        $request->setPreviousUrl("http://bar.com");
        $this->assertEquals("http://bar.com", $request->getPreviousUrl());
    }

    /**
     * Tests that the query is given preference when getting input
     */
    public function testQueryIsGivenPreferenceWhenGettingInput()
    {
        $_GET["foo"] = "bar";
        $_POST["foo"] = "baz";
        $request = Request::createFromGlobals();
        $this->assertEquals("bar", $request->getInput("foo"));
    }

    /**
     * Tests that the query is returned when getting input
     */
    public function testQueryIsReturnedFromInput()
    {
        $_GET["foo"] = "bar";
        $request = Request::createFromGlobals();
        $this->assertEquals("bar", $request->getInput("foo"));
    }

    /**
     * Tests setting the method
     */
    public function testSettingMethod()
    {
        $this->request->setMethod("put");
        $this->assertEquals("PUT", $this->request->getMethod());
    }

    /**
     * Tests setting the path
     */
    public function testSettingPath()
    {
        $this->request->setPath("/foo");
        $this->assertEquals("/foo", $this->request->getPath());
    }

    /**
     * Tests that the port is removed from the host
     */
    public function testThatPortIsRemovedFromHost()
    {
        $_SERVER["HTTP_HOST"] = "foo.com:8080";
        $request = Request::createFromGlobals();
        $this->assertEquals("foo.com", $request->getHost());
    }
} 
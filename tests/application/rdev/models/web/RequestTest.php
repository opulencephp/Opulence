<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the HTTP request
 */
namespace RDev\Models\Web;

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
     * Tests checking that a set COOKIE is set
     */
    public function testCheckingIfSetCookieIsSet()
    {
        $request = new Request();
        $_COOKIE["foo"] = "bar";
        $this->assertTrue($request->cookieIsSet("foo"));
    }

    /**
     * Tests checking that a set GET variable is set
     */
    public function testCheckingIfSetGetVarIsSet()
    {
        $request = new Request();
        $_GET["foo"] = "bar";
        $this->assertTrue($request->queryStringVarIsSet("foo"));
    }

    /**
     * Tests checking that a set POST variable is set
     */
    public function testCheckingIfSetPostVarIsSet()
    {
        $request = new Request();
        $_POST["foo"] = "bar";
        $this->assertTrue($request->postVarIsSet("foo"));
    }

    /**
     * Tests checking that an unset COOKIE is set
     */
    public function testCheckingIfUnsetCookieIsSet()
    {
        $request = new Request();
        $this->assertFalse($request->cookieIsSet("foo"));
    }

    /**
     * Tests checking that an unset GET variable is not set
     */
    public function testCheckingIfUnsetGetVarIsNotSet()
    {
        $request = new Request();
        $this->assertFalse($request->queryStringVarIsSet("foo"));
    }

    /**
     * Tests checking that an unset POST variable is not set
     */
    public function testCheckingIfUnsetPostVarIsNotSet()
    {
        $request = new Request();
        $this->assertFalse($request->postVarIsSet("foo"));
    }

    /**
     * Tests getting the connect method
     */
    public function testGettingConnectMethod()
    {
        $_SERVER["REQUEST_METHOD"] = "CONNECT";
        $request = new Request();
        $this->assertEquals(Request::METHOD_CONNECT, $request->getMethod());
    }

    /**
     * Tests getting the delete method
     */
    public function testGettingDeleteMethod()
    {
        $_SERVER["REQUEST_METHOD"] = "DELETE";
        $request = new Request();
        $this->assertEquals(Request::METHOD_DELETE, $request->getMethod());
    }

    /**
     * Tests getting the get method
     */
    public function testGettingGetMethod()
    {
        $_SERVER["REQUEST_METHOD"] = "GET";
        $request = new Request();
        $this->assertEquals(Request::METHOD_GET, $request->getMethod());
    }

    /**
     * Tests getting the IP address
     */
    public function testGettingIPAddress()
    {
        $defaultIPAddress = "120.138.20.36";
        $keys = ["HTTP_CLIENT_IP", "HTTP_X_FORWARDED_FOR", "HTTP_X_FORWARDED", "HTTP_X_CLUSTER_CLIENT_IP",
            "HTTP_FORWARDED_FOR", "HTTP_FORWARDED", "REMOTE_ADDR"];

        // Delete all the keys that might hold an IP address
        foreach($keys as $key)
        {
            unset($_SERVER[$key]);
        }

        // Set each key and try getting the IP address using it
        foreach($keys as $key)
        {
            $_SERVER[$key] = $defaultIPAddress;
            $request = new Request();
            $this->assertEquals($defaultIPAddress, $request->getIPAddress());
            unset($_SERVER[$key]);
        }
    }

    /**
     * Tests getting the method when the input method doesn't match any predefined values
     */
    public function testGettingMethodWhenInputDoesNotMatchPredefinedValue()
    {
        $_SERVER["REQUEST_METHOD"] = "foo";
        $request = new Request();
        $this->assertEquals(Request::METHOD_GET, $request->getMethod());
    }

    /**
     * Tests getting the method when there is none set in the $_SERVER
     */
    public function testGettingMethodWhenNoneIsSet()
    {
        unset($_SERVER["REQUEST_METHOD"]);
        $request = new Request();
        $this->assertEquals(Request::METHOD_GET, $request->getMethod());
    }

    /**
     * Tests getting the options method
     */
    public function testGettingOptionsMethod()
    {
        $_SERVER["REQUEST_METHOD"] = "OPTIONS";
        $request = new Request();
        $this->assertEquals(Request::METHOD_OPTIONS, $request->getMethod());
    }

    /**
     * Tests getting the patch method
     */
    public function testGettingPatchMethod()
    {
        $_SERVER["REQUEST_METHOD"] = "PATCH";
        $request = new Request();
        $this->assertEquals(Request::METHOD_PATCH, $request->getMethod());
    }

    /**
     * Tests getting the post method
     */
    public function testGettingPostMethod()
    {
        $_SERVER["REQUEST_METHOD"] = "POST";
        $request = new Request();
        $this->assertEquals(Request::METHOD_POST, $request->getMethod());
    }

    /**
     * Tests getting the purge method
     */
    public function testGettingPurgeMethod()
    {
        $_SERVER["REQUEST_METHOD"] = "PURGE";
        $request = new Request();
        $this->assertEquals(Request::METHOD_PURGE, $request->getMethod());
    }

    /**
     * Tests getting the put method
     */
    public function testGettingPutMethod()
    {
        $_SERVER["REQUEST_METHOD"] = "PUT";
        $request = new Request();
        $this->assertEquals(Request::METHOD_PUT, $request->getMethod());
    }

    /**
     * Tests getting the query string
     */
    public function testGettingQueryString()
    {
        $queryString = "foo=bar&blah=asdf";
        $_SERVER["QUERY_STRING"] = $queryString;
        $request = new Request();
        $this->assertEquals($queryString, $request->getQueryString());
    }

    /**
     * Tests getting the request URI
     */
    public function testGettingRequestURI()
    {
        $_SERVER["REQUEST_URI"] = "/foo/bar";
        $request = new Request();
        $this->assertEquals("/foo/bar", $request->getRequestURI());
    }

    /**
     * Tests getting the request URI when none was set
     */
    public function testGettingRequestURIWhenNoneWasSet()
    {
        unset($_SERVER["REQUEST_URI"]);
        $request = new Request();
        $this->assertEmpty($request->getRequestURI());
    }

    /**
     * Tests getting a set cookie
     */
    public function testGettingSetCookie()
    {
        $request = new Request();
        $_COOKIE["foo"] = "bar";
        $this->assertEquals("bar", $request->getCookie("foo"));
    }

    /**
     * Tests getting a set GET variable
     */
    public function testGettingSetGetVar()
    {
        $request = new Request();
        $_GET["foo"] = "bar";
        $this->assertEquals("bar", $request->getQueryStringVar("foo"));
    }

    /**
     * Tests getting a set POST variable
     */
    public function testGettingSetPostVar()
    {
        $request = new Request();
        $_POST["foo"] = "bar";
        $this->assertEquals("bar", $request->getPostVar("foo"));
    }

    /**
     * Tests getting the trace method
     */
    public function testGettingTraceMethod()
    {
        $_SERVER["REQUEST_METHOD"] = "TRACE";
        $request = new Request();
        $this->assertEquals(Request::METHOD_TRACE, $request->getMethod());
    }

    /**
     * Tests getting an unset cookie
     */
    public function testGettingUnsetCookie()
    {
        $request = new Request();
        $this->assertFalse($request->getCookie("foo"));
    }

    /**
     * Tests getting an unset GET variable
     */
    public function testGettingUnsetGetVar()
    {
        $request = new Request();
        $this->assertFalse($request->getQueryStringVar("foo"));
    }

    /**
     * Tests getting an unset POST variable
     */
    public function testGettingUnsetPostVar()
    {
        $request = new Request();
        $this->assertFalse($request->getPostVar("foo"));
    }

    /**
     * Tests getting the user agent
     */
    public function testGettingUserAgent()
    {
        $fakeUserAgent = "foobar";
        $_SERVER["HTTP_USER_AGENT"] = $fakeUserAgent;
        $request = new Request();
        $this->assertEquals($fakeUserAgent, $request->getUserAgent());
    }
} 
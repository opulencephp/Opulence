<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the HTTP request
 */
namespace RDev\HTTP\Requests;

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
     * Tests checking that an unset POST variable is not set
     */
    public function testCheckingIfUnsetPostVarIsNotSet()
    {
        $this->assertFalse($this->request->getPost()->has("foo"));
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
     * Tests creating from globals
     */
    public function testCreatingFromGlobals()
    {
        $requestFromConstructor = new Request($_GET, $_POST, $_COOKIE, $_SERVER, $_FILES, $_ENV);
        $this->assertEquals($requestFromConstructor, Request::createFromGlobals());
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
        $this->assertSame($_FILES, $this->request->getFiles()->getAll());
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
        foreach($this->request->getServer()->getAll() as $key => $value)
        {
            if(strpos($key, "HTTP_") === 0)
            {
                $headerParameters[substr($key, 5)] = $value;
            }
        }

        $this->assertSame($headerParameters, $this->request->getHeaders()->getAll());
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
            $request = Request::createFromGlobals();
            $this->assertEquals($defaultIPAddress, $request->getIPAddress());
            unset($_SERVER[$key]);
        }
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
     * Tests getting the options method
     */
    public function testGettingOptionsMethod()
    {
        $_SERVER["REQUEST_METHOD"] = "OPTIONS";
        $this->request->getServer()->exchangeArray($_SERVER);
        $this->assertEquals(Request::METHOD_OPTIONS, $this->request->getServer()->get("REQUEST_METHOD"));
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
    }
} 
<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the HTTP request
 */
namespace RDev\Models\Web;

class RequestTest extends \PHPUnit_Framework_TestCase
{
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
        self::$serverClone = $_SERVER;
        self::$getClone = $_GET;
        self::$postClone = $_POST;
    }

    /**
     * Does some housekeeping before ending the tests
     */
    public function tearDown()
    {
        $_SERVER = self::$serverClone;
        $_GET = self::$getClone;
        $_POST = self::$postClone;
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
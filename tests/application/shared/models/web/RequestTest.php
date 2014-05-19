<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the HTTP request
 */
namespace RDev\Application\Shared\Models\Web;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var array A clone of the $_SERVER array, which we can use to restore original values */
    private $serverClone = [];

    /**
     * Sets up our tests
     */
    public function setUp()
    {
        $this->serverClone = $_SERVER;
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

        // Reset our server array for other tests
        $_SERVER = $this->serverClone;
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
        // Reset our server array for other tests
        $_SERVER = $this->serverClone;
    }
} 
<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the HTTP response
 */
namespace RDev\Models\Web;
use RDev\Tests\Models\Web\Mocks;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /** @var Mocks\Response The response to use in tests */
    private $response = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->response = new Mocks\Response();
    }

    /**
     * Tests deleting a cookie
     */
    public function testDeletingCookie()
    {
        $this->response->setCookie("foo", "bar", new \DateTime("now", new \DateTimeZone("UTC")), "/", "foo.bar", true, true);
        $this->response->deleteCookie("foo", "/", "foo.bar", true, true);
        $this->assertEmpty($this->response->getCookies()["foo"]["value"]);
    }

    /**
     * Tests setting a cookie
     */
    public function testSettingCookie()
    {
        $this->response->setCookie("foo", "bar", new \DateTime("now", new \DateTimeZone("UTC")), "/", "foo.bar", true, true);
        $this->assertEquals("bar", $this->response->getCookies()["foo"]["value"]);
    }

    /**
     * Tests setting an expiration
     */
    public function testSettingExpiration()
    {
        $expiration = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->response->setExpiration($expiration);
        $this->assertEquals([
            "value" => $expiration->format("r"),
            "shouldReplace" => true,
            "httpResponseCode" => ""
        ], $this->response->getHeaders()["Expires"]);
    }

    /**
     * Tests setting a header
     */
    public function testSettingHeader()
    {
        $this->response->setHeader("foo", "bar", false, 400);
        $this->assertEquals([
            "value" => "bar",
            "shouldReplace" => false,
            "httpResponseCode" => 400
        ], $this->response->getHeaders()["foo"]);
    }

    /**
     * Tests setting a location
     */
    public function testSettingLocation()
    {
        $location = "http://www.google.com";
        $this->response->setLocation($location);
        $this->assertEquals([
            "value" => $location,
            "shouldReplace" => true,
            "httpResponseCode" => ""
        ], $this->response->getHeaders()["Location"]);
    }
}
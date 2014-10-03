<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the response headers
 */
namespace RDev\Models\Web;

class ResponseHeadersTest extends \PHPUnit_Framework_TestCase
{
    /** @var ResponseHeaders The headers to use in tests */
    private $headers = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->headers = new ResponseHeaders();
    }

    /**
     * Tests deleting a cookie
     */
    public function testDeletingCookie()
    {
        $cookie = new Cookie("foo", "bar", new \DateTime("+1 week", new \DateTimeZone("UTC")));
        $this->headers->setCookie($cookie);
        $this->headers->deleteCookie($cookie->getName(), $cookie->getPath(), $cookie->getDomain());
        $this->assertEmpty($this->headers->getCookies());
    }

    /**
     * Tests getting all the cookies
     */
    public function testGettingCookies()
    {
        $cookie1 = new Cookie("foo", "bar", new \DateTime("+1 week", new \DateTimeZone("UTC")));
        $cookie2 = new Cookie("bar", "foo", new \DateTime("+2 weeks", new \DateTimeZone("UTC")));
        $this->headers->setCookie($cookie1);
        $this->headers->setCookie($cookie2);
        $this->assertEquals([$cookie1, $cookie2], $this->headers->getCookies());
    }

    /**
     * Tests setting a cookie
     */
    public function testSettingCookie()
    {
        $cookie = new Cookie("foo", "bar", new \DateTime("+1 week", new \DateTimeZone("UTC")));
        $this->headers->setCookie($cookie);
        $this->assertEquals([$cookie], $this->headers->getCookies());
    }
} 
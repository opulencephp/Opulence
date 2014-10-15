<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the response headers
 */
namespace RDev\Models\HTTP;

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
        $this->assertEquals(1, count($this->headers->getCookies(true)));
    }

    /**
     * Tests deleting multiple cookies
     */
    public function testDeletingMultipleCookies()
    {
        $cookie1 = new Cookie("foo", "bar", new \DateTime("+1 week", new \DateTimeZone("UTC")));
        $cookie2 = new Cookie("bar", "foo", new \DateTime("+1 week", new \DateTimeZone("UTC")));
        $this->headers->setCookie($cookie1);
        $this->headers->setCookie($cookie2);
        $this->headers->deleteCookie("foo");
        $this->headers->deleteCookie("bar");
        $deletedCookies = $this->headers->getCookies(true);
        $this->assertEquals(2, count($deletedCookies));
        $this->assertEquals("foo", $deletedCookies[0]->getName());
        $this->assertEquals("bar", $deletedCookies[1]->getName());
    }

    /**
     * Tests getting all the cookies
     */
    public function testGettingCookies()
    {
        $cookie1 = new Cookie("foo", "bar", new \DateTime("+1 week", new \DateTimeZone("UTC")));
        $cookie2 = new Cookie("bar", "foo", new \DateTime("+2 weeks", new \DateTimeZone("UTC")));
        $cookie3 = new Cookie("baz", "foo", new \DateTime("-1 weeks", new \DateTimeZone("UTC")));
        $this->headers->setCookie($cookie1);
        $this->headers->setCookie($cookie2);
        $this->headers->setCookie($cookie3);
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

    /**
     * Tests setting multiple cookies
     */
    public function testSettingMultipleCookies()
    {
        $cookie1 = new Cookie("foo", "bar", new \DateTime("+1 week", new \DateTimeZone("UTC")));
        $cookie2 = new Cookie("bar", "foo", new \DateTime("+1 week", new \DateTimeZone("UTC")));
        $this->headers->setCookies([$cookie1, $cookie2]);
        $this->assertEquals([$cookie1, $cookie2], $this->headers->getCookies());
    }
} 
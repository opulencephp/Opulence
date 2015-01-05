<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the cookie class
 */
namespace RDev\HTTP;

class CookieTest extends \PHPUnit_Framework_TestCase
{
    /** @var Cookie The cookie to test */
    private $cookie = null;
    /** @var string The name of the cookie */
    private $name = "";
    /** @var mixed The value of the cookie */
    private $value = "";
    /** @var \DateTime The expiration of the cookie */
    private $expiration = null;
    /** @var string The path the cookie is valid on */
    private $path = "/";
    /** @var string The domain the cookie is valid on */
    private $domain = "";
    /** @var bool Whether or not this cookie is on HTTPS */
    private $isSecure = false;
    /** @var bool Whether or not this cookie is HTTP only */
    private $isHTTPOnly = true;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->name = "foo";
        $this->value = "bar";
        $this->expiration = new \DateTime("+1 week");
        $this->path = "/foo";
        $this->domain = "foo.com";
        $this->isSecure = true;
        $this->isHTTPOnly = true;
        $this->cookie = new Cookie($this->name, $this->value, $this->expiration, $this->path, $this->domain,
            $this->isSecure, $this->isHTTPOnly);
    }

    /**
     * Tests getting the domain
     */
    public function testGettingDomain()
    {
        $this->assertEquals($this->domain, $this->cookie->getDomain());
    }

    /**
     * Tests getting the expiration
     */
    public function testGettingExpiration()
    {
        $this->assertEquals($this->expiration, $this->cookie->getExpiration());
    }

    /**
     * Tests getting the HTTP-only flag
     */
    public function testGettingIsHTTPOnly()
    {
        $this->assertEquals($this->isHTTPOnly, $this->cookie->isHTTPOnly());
    }

    /**
     * Tests getting the secure flag
     */
    public function testGettingIsSecure()
    {
        $this->assertEquals($this->isSecure, $this->cookie->isSecure());
    }

    /**
     * Tests getting the name
     */
    public function testGettingName()
    {
        $this->assertEquals($this->name, $this->cookie->getName());
    }

    /**
     * Tests getting the path
     */
    public function testGettingPath()
    {
        $this->assertEquals($this->path, $this->cookie->getPath());
    }

    /**
     * Tests getting the value
     */
    public function testGettingValue()
    {
        $this->assertEquals($this->value, $this->cookie->getValue());
    }

    /**
     * Tests setting the domain
     */
    public function testSettingDomain()
    {
        $this->cookie->setDomain("blah.com");
        $this->assertEquals("blah.com", $this->cookie->getDomain());
    }

    /**
     * Tests setting the expiration
     */
    public function testSettingExpiration()
    {
        $expiration = new \DateTime("+1 day");
        $this->cookie->setExpiration($expiration);
        $this->assertEquals($expiration, $this->cookie->getExpiration());
    }

    /**
     * Tests setting the HTTP-only flag
     */
    public function testSettingIsHTTPOnly()
    {
        $this->cookie->setHTTPOnly(true);
        $this->assertTrue($this->cookie->isHTTPOnly());
    }

    /**
     * Tests setting the secure flag
     */
    public function testSettingIsSecure()
    {
        $this->cookie->setSecure(false);
        $this->assertFalse($this->cookie->isSecure());
    }

    /**
     * Tests setting the name
     */
    public function testSettingName()
    {
        $this->cookie->setName("blah");
        $this->assertEquals("blah", $this->cookie->getName());
    }

    /**
     * Tests setting the path
     */
    public function testSettingPath()
    {
        $this->cookie->setPath("blah");
        $this->assertEquals("blah", $this->cookie->getPath());
    }

    /**
     * Tests setting the value
     */
    public function testSettingValue()
    {
        $this->cookie->setValue("blah");
        $this->assertEquals("blah", $this->cookie->getValue());
    }
} 
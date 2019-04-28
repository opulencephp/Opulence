<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Http\Tests\Responses;

use DateTime;
use Opulence\Http\Responses\Cookie;

/**
 * Tests the cookie class
 */
class CookieTest extends \PHPUnit\Framework\TestCase
{
    /** @var Cookie The cookie to test */
    private $cookie;
    /** @var string The name of the cookie */
    private $name;
    /** @var mixed The value of the cookie */
    private $value;
    /** @var DateTime The expiration of the cookie */
    private $expiration;
    /** @var string The path the cookie is valid on */
    private $path = '/';
    /** @var string The domain the cookie is valid on */
    private $domain = '';
    /** @var bool Whether or not this cookie is on HTTPS */
    private $isSecure = false;
    /** @var bool Whether or not this cookie is HTTP only */
    private $isHttpOnly = true;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->name = 'foo';
        $this->value = 'bar';
        $this->expiration = new DateTime('+1 week');
        $this->path = '/foo';
        $this->domain = 'foo.com';
        $this->isSecure = true;
        $this->isHttpOnly = true;
        $this->cookie = new Cookie(
            $this->name,
            $this->value,
            $this->expiration,
            $this->path,
            $this->domain,
            $this->isSecure,
            $this->isHttpOnly
        );
    }

    /**
     * Tests getting the domain
     */
    public function testGettingDomain(): void
    {
        $this->assertEquals($this->domain, $this->cookie->getDomain());
    }

    /**
     * Tests getting the expiration
     */
    public function testGettingExpiration(): void
    {
        $this->assertEquals($this->expiration->format('U'), $this->cookie->getExpiration());
    }

    /**
     * Tests getting the HTTP-only flag
     */
    public function testGettingIsHttpOnly(): void
    {
        $this->assertEquals($this->isHttpOnly, $this->cookie->isHttpOnly());
    }

    /**
     * Tests getting the secure flag
     */
    public function testGettingIsSecure(): void
    {
        $this->assertEquals($this->isSecure, $this->cookie->isSecure());
    }

    /**
     * Tests getting the name
     */
    public function testGettingName(): void
    {
        $this->assertEquals($this->name, $this->cookie->getName());
    }

    /**
     * Tests getting the path
     */
    public function testGettingPath(): void
    {
        $this->assertEquals($this->path, $this->cookie->getPath());
    }

    /**
     * Tests getting the value
     */
    public function testGettingValue(): void
    {
        $this->assertEquals($this->value, $this->cookie->getValue());
    }

    /**
     * Tests passing a timestamp for the expiration
     */
    public function testPassingTimestampForExpiration(): void
    {
        $time = time();
        $cookie = new Cookie($this->name, $this->value, $time);
        $this->assertEquals($time, $cookie->getExpiration());
    }

    /**
     * Tests setting the domain
     */
    public function testSettingDomain(): void
    {
        $this->cookie->setDomain('blah.com');
        $this->assertEquals('blah.com', $this->cookie->getDomain());
    }

    /**
     * Tests setting the expiration
     */
    public function testSettingExpiration(): void
    {
        $expiration = new DateTime('+1 day');
        $this->cookie->setExpiration($expiration);
        $this->assertEquals($expiration->format('U'), $this->cookie->getExpiration());
    }

    /**
     * Tests setting the HTTP-only flag
     */
    public function testSettingIsHttpOnly(): void
    {
        $this->cookie->setHttpOnly(true);
        $this->assertTrue($this->cookie->isHttpOnly());
    }

    /**
     * Tests setting the secure flag
     */
    public function testSettingIsSecure(): void
    {
        $this->cookie->setSecure(false);
        $this->assertFalse($this->cookie->isSecure());
    }

    /**
     * Tests setting the name
     */
    public function testSettingName(): void
    {
        $this->cookie->setName('blah');
        $this->assertEquals('blah', $this->cookie->getName());
    }

    /**
     * Tests setting the path
     */
    public function testSettingPath(): void
    {
        $this->cookie->setPath('blah');
        $this->assertEquals('blah', $this->cookie->getPath());
    }

    /**
     * Tests setting the value
     */
    public function testSettingValue(): void
    {
        $this->cookie->setValue('blah');
        $this->assertEquals('blah', $this->cookie->getValue());
    }
}

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
use Opulence\Http\Responses\ResponseHeaders;

/**
 * Tests the response headers
 */
class ResponseHeadersTest extends \PHPUnit\Framework\TestCase
{
    /** @var ResponseHeaders The headers to use in tests */
    private $headers;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->headers = new ResponseHeaders();
    }

    /**
     * Tests deleting a cookie
     */
    public function testDeletingCookie(): void
    {
        $cookie = new Cookie('foo', 'bar', new DateTime('+1 week'));
        $this->headers->setCookie($cookie);
        $this->headers->deleteCookie($cookie->getName(), $cookie->getPath(), $cookie->getDomain());
        $this->assertEmpty($this->headers->getCookies());
        $this->assertCount(1, $this->headers->getCookies(true));
    }

    /**
     * Tests deleting multiple cookies
     */
    public function testDeletingMultipleCookies(): void
    {
        $cookie1 = new Cookie('foo', 'bar', new DateTime('+1 week'));
        $cookie2 = new Cookie('bar', 'foo', new DateTime('+1 week'));
        $this->headers->setCookie($cookie1);
        $this->headers->setCookie($cookie2);
        $this->headers->deleteCookie('foo');
        $this->headers->deleteCookie('bar');
        $deletedCookies = $this->headers->getCookies(true);
        $this->assertCount(2, $deletedCookies);
        $this->assertEquals('foo', $deletedCookies[0]->getName());
        $this->assertEquals('bar', $deletedCookies[1]->getName());
    }

    /**
     * Tests getting all the cookies
     */
    public function testGettingCookies(): void
    {
        $cookie1 = new Cookie('foo', 'bar', new DateTime('+1 week'));
        $cookie2 = new Cookie('bar', 'foo', new DateTime('+2 weeks'));
        $cookie3 = new Cookie('baz', 'foo', new DateTime('-1 weeks'));
        $this->headers->setCookie($cookie1);
        $this->headers->setCookie($cookie2);
        $this->headers->setCookie($cookie3);
        $this->assertEquals([$cookie1, $cookie2], $this->headers->getCookies());
    }

    /**
     * Tests setting a cookie
     */
    public function testSettingCookie(): void
    {
        $cookie = new Cookie('foo', 'bar', new DateTime('+1 week'));
        $this->headers->setCookie($cookie);
        $this->assertEquals([$cookie], $this->headers->getCookies());
    }

    /**
     * Tests setting multiple cookies
     */
    public function testSettingMultipleCookies(): void
    {
        $cookie1 = new Cookie('foo', 'bar', new DateTime('+1 week'));
        $cookie2 = new Cookie('bar', 'foo', new DateTime('+1 week'));
        $this->headers->setCookies([$cookie1, $cookie2]);
        $this->assertEquals([$cookie1, $cookie2], $this->headers->getCookies());
    }
}

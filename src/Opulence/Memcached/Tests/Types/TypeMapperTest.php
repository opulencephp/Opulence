<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Memcached\Tests\Types;

use DateTime;
use DateTimeImmutable;
use Opulence\Memcached\Types\TypeMapper;

/**
 * Tests the Memcached type mapper class
 */
class TypeMapperTest extends \PHPUnit\Framework\TestCase
{
    /** @var TypeMapper The type mapper to use for tests */
    private $typeMapper = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->typeMapper = new TypeMapper();
    }

    /**
     * Tests converting from a false Memcached boolean
     */
    public function testConvertingFromFalseMemcachedBoolean()
    {
        $this->assertFalse($this->typeMapper->fromMemcachedBoolean(0));
    }

    /**
     * Tests converting from a Memcached timestamp
     */
    public function testConvertingFromMemcachedTimestamp()
    {
        $time = new DateTime('now');
        $this->assertEquals($time->getTimestamp(),
            $this->typeMapper->fromMemcachedTimestamp($time->getTimestamp())->getTimestamp());
    }

    /**
     * Tests converting from a true Memcached boolean
     */
    public function testConvertingFromTrueMemcachedBoolean()
    {
        $this->assertTrue($this->typeMapper->fromMemcachedBoolean(1));
    }

    /**
     * Tests converting to a false Memcached boolean
     */
    public function testConvertingToFalseMemcachedBoolean()
    {
        $this->assertSame(0, $this->typeMapper->toMemcachedBoolean(false));
    }

    /**
     * Tests converting to a Memcached timestamp
     */
    public function testConvertingToMemcachedTimestamp()
    {
        $time = new DateTime('now');
        $this->assertEquals($time->getTimestamp(), $this->typeMapper->toMemcachedTimestamp($time));
    }

    /**
     * Tests converting to a Memcached timestamp from an immutable date time
     */
    public function testConvertingToMemcachedTimestampFromImmutable()
    {
        $time = new DateTimeImmutable('now');
        $this->assertEquals($time->getTimestamp(), $this->typeMapper->toMemcachedTimestamp($time));
    }

    /**
     * Tests converting to a true Memcached boolean
     */
    public function testConvertingToTrueMemcachedBoolean()
    {
        $this->assertSame(1, $this->typeMapper->toMemcachedBoolean(true));
    }

    /**
     * Tests that the timezone is set
     */
    public function testTimezoneSet()
    {
        $currTimezone = date_default_timezone_get();
        $newTimezone = 'Australia/Canberra';
        date_default_timezone_set($newTimezone);
        $time = new DateTime('now');
        $memcachedTime = $this->typeMapper->fromMemcachedTimestamp($time->getTimestamp());
        $this->assertEquals($newTimezone, $memcachedTime->getTimezone()->getName());
        // Reset the timezone
        date_default_timezone_set($currTimezone);
    }
}

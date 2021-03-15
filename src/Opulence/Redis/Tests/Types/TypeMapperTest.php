<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Redis\Tests\Types;

use DateTime;
use DateTimeImmutable;
use Opulence\Redis\Types\TypeMapper;

/**
 * Tests the Redis type mapper class
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
     * Tests converting from a false Redis boolean
     */
    public function testConvertingFromFalseRedisBoolean()
    {
        $this->assertFalse($this->typeMapper->fromRedisBoolean(0));
    }

    /**
     * Tests converting from a Redis timestamp
     */
    public function testConvertingFromRedisTimestamp()
    {
        $time = new DateTime('now');
        $this->assertEquals($time->getTimestamp(),
            $this->typeMapper->fromRedisTimestamp($time->getTimestamp())->getTimestamp());
    }

    /**
     * Tests converting from a true Redis boolean
     */
    public function testConvertingFromTrueRedisBoolean()
    {
        $this->assertTrue($this->typeMapper->fromRedisBoolean(1));
    }

    /**
     * Tests converting to a false Redis boolean
     */
    public function testConvertingToFalseRedisBoolean()
    {
        $this->assertSame(0, $this->typeMapper->toRedisBoolean(false));
    }

    /**
     * Tests converting to a Redis timestamp
     */
    public function testConvertingToRedisTimestamp()
    {
        $time = new DateTime('now');
        $this->assertEquals($time->getTimestamp(), $this->typeMapper->toRedisTimestamp($time));
    }

    /**
     * Tests converting to a Redis timestamp from an immutable date time
     */
    public function testConvertingToRedisTimestampFromImmutable()
    {
        $time = new DateTimeImmutable('now');
        $this->assertEquals($time->getTimestamp(), $this->typeMapper->toRedisTimestamp($time));
    }

    /**
     * Tests converting to a true Redis boolean
     */
    public function testConvertingToTrueRedisBoolean()
    {
        $this->assertSame(1, $this->typeMapper->toRedisBoolean(true));
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
        $redisTime = $this->typeMapper->fromRedisTimestamp($time->getTimestamp());
        $this->assertEquals($newTimezone, $redisTime->getTimezone()->getName());
        // Reset the timezone
        date_default_timezone_set($currTimezone);
    }
}

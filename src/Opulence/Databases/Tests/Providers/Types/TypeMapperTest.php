<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Databases\Tests\Providers\Types;

use DateTime;
use Opulence\Databases\Providers\Provider;
use Opulence\Databases\Providers\Types\TypeMapper;
use RuntimeException;

/**
 * Tests the type mapper class
 */
class TypeMapperTest extends \PHPUnit\Framework\TestCase
{
    /** @var TypeMapper The type mapper without a provider to use for tests */
    private $typeMapperWithNoProvider = null;
    /** @var TypeMapper The type mapper with a provider to use for tests */
    private $typeMapperWithProvider = null;
    /** @var Provider The provider to use for tests */
    private $provider = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->typeMapperWithNoProvider = new TypeMapper();
        $this->provider = new Provider();
        $this->typeMapperWithProvider = new TypeMapper($this->provider);
    }

    /**
     * Tests converting from a null date returns null
     */
    public function testConvertingDateFromNullReturnsNull()
    {
        $this->assertNull($this->typeMapperWithNoProvider->fromSqlDate(null, $this->provider));
        $this->assertNull($this->typeMapperWithProvider->fromSqlDate(null));
    }

    /**
     * Tests converting from a false SQL boolean
     */
    public function testConvertingFromFalseSqlBoolean()
    {
        $sqlBoolean = 0;
        $this->assertFalse($this->typeMapperWithNoProvider->fromSqlBoolean($sqlBoolean, $this->provider));
        $this->assertFalse($this->typeMapperWithProvider->fromSqlBoolean($sqlBoolean));
    }

    /**
     * Tests converting from an invalid SQL timestamp without time zone
     */
    public function testConvertingFromInvalidSqlTimestampWithoutTimeZone()
    {
        $exceptionsThrown = false;

        try {
            $this->typeMapperWithNoProvider->fromSqlTimestampWithoutTimeZone('not a real timestamp', $this->provider);
        } catch (\InvalidArgumentException $ex) {
            $exceptionsThrown = true;
        }

        try {
            $this->typeMapperWithProvider->fromSqlTimestampWithoutTimeZone('not a real timestamp');
        } catch (\InvalidArgumentException $ex) {
            $exceptionsThrown = $exceptionsThrown && true;
        }

        $this->assertTrue($exceptionsThrown);
    }

    /**
     * Tests converting from an SQL date
     */
    public function testConvertingFromSqlDate()
    {
        $phpDate = new DateTime('now');
        $sqlDate = $phpDate->format($this->provider->getDateFormat());
        // Compare the formatted string because testing sometimes leads to the type mapper's date to be one second later
        $this->assertEquals(
            $phpDate->format('Ymd'),
            $this->typeMapperWithNoProvider->fromSqlDate($sqlDate, $this->provider)->format('Ymd')
        );
        // Make sure the hour, minutes, and seconds are zeroed out
        $this->assertEquals('000000',
            $this->typeMapperWithNoProvider->fromSqlDate($sqlDate, $this->provider)->format('His'));
        $this->assertEquals(
            $phpDate->format('Ymd'),
            $this->typeMapperWithProvider->fromSqlDate($sqlDate)->format('Ymd')
        );
        // Make sure the hour, minutes, and seconds are zeroed out
        $this->assertEquals('000000', $this->typeMapperWithProvider->fromSqlDate($sqlDate)->format('His'));
    }

    /**
     * Tests converting from an SQL time with time zone
     */
    public function testConvertingFromSqlTimeWithTimeZone()
    {
        $phpTimeWithMicroseconds = new DateTime('now');
        $sqlTimeWithMicroseconds = $phpTimeWithMicroseconds->format('H:i:s.uP');
        $this->assertEquals($phpTimeWithMicroseconds->getTimestamp(), $this->typeMapperWithNoProvider
            ->fromSqlTimestampWithTimeZone($sqlTimeWithMicroseconds, $this->provider)->getTimestamp());
        $this->assertEquals($phpTimeWithMicroseconds->getTimestamp(), $this->typeMapperWithProvider
            ->fromSqlTimestampWithTimeZone($sqlTimeWithMicroseconds)->getTimestamp());
        $phpTime = new DateTime('now');
        $sqlTime = $phpTime->format($this->provider->getTimeWithTimeZoneFormat());
        $this->assertEquals($phpTime->getTimestamp(),
            $this->typeMapperWithNoProvider->fromSqlTimeWithTimeZone($sqlTime, $this->provider)->getTimestamp());
        $this->assertEquals($phpTime->getTimestamp(),
            $this->typeMapperWithProvider->fromSqlTimeWithTimeZone($sqlTime)->getTimestamp());
    }

    /**
     * Tests converting from an SQL time without time zone
     */
    public function testConvertingFromSqlTimeWithoutTimeZone()
    {
        $phpTimeWithMicroseconds = new DateTime('now');
        $sqlTimeWithMicroseconds = $phpTimeWithMicroseconds->format('H:i:s.u');
        $this->assertEquals($phpTimeWithMicroseconds, $this->typeMapperWithNoProvider
            ->fromSqlTimestampWithTimeZone($sqlTimeWithMicroseconds, $this->provider));
        $this->assertEquals($phpTimeWithMicroseconds, $this->typeMapperWithProvider
            ->fromSqlTimestampWithTimeZone($sqlTimeWithMicroseconds));
        $phpTime = new DateTime('now');
        $sqlTime = $phpTime->format($this->provider->getTimeWithoutTimeZoneFormat());
        // We do the flooring of the timestamp because, since PHP 7.1, new DateTime("now") contains microseconds
        $this->assertEquals(
            floor($phpTime->getTimestamp()),
            floor($this->typeMapperWithNoProvider->fromSqlTimeWithoutTimeZone($sqlTime,
                $this->provider)->getTimestamp())
        );
        $this->assertEquals(
            floor($phpTime->getTimestamp()),
            floor($this->typeMapperWithProvider->fromSqlTimeWithoutTimeZone($sqlTime)->getTimestamp())
        );
    }

    /**
     * Tests converting from an SQL timestamp with time zone
     */
    public function testConvertingFromSqlTimestampWithTimeZone()
    {
        $phpTimestampWithMicroseconds = new DateTime('now');
        $sqlTimestampWithMicroseconds = $phpTimestampWithMicroseconds->format('Y-m-d H:i:s.uP');
        $this->assertEquals($phpTimestampWithMicroseconds->getTimestamp(), $this->typeMapperWithNoProvider
            ->fromSqlTimestampWithTimeZone($sqlTimestampWithMicroseconds, $this->provider)->getTimestamp());
        $this->assertEquals($phpTimestampWithMicroseconds->getTimestamp(), $this->typeMapperWithProvider
            ->fromSqlTimestampWithTimeZone($sqlTimestampWithMicroseconds)->getTimestamp());
        $phpTimestamp = new DateTime('now');
        $sqlTimestamp = $phpTimestamp->format($this->provider->getTimestampWithTimeZoneFormat());
        // We do the flooring of the timestamp because, since PHP 7.1, new DateTime("now") contains microseconds
        $this->assertEquals(
            floor($phpTimestamp->getTimestamp()),
            floor($this->typeMapperWithNoProvider->fromSqlTimestampWithTimeZone($sqlTimestamp,
                $this->provider)->getTimestamp()));
        $this->assertEquals(
            floor($phpTimestamp->getTimestamp()),
            floor($this->typeMapperWithProvider->fromSqlTimestampWithTimeZone($sqlTimestamp)->getTimestamp()));
    }

    /**
     * Tests converting from an SQL timestamp without time zone
     */
    public function testConvertingFromSqlTimestampWithoutTimeZone()
    {
        $phpTimestampWithMicroseconds = new DateTime('now');
        $sqlTimestampWithMicroseconds = $phpTimestampWithMicroseconds->format('Y-m-d H:i:s.u');
        $this->assertEquals($phpTimestampWithMicroseconds, $this->typeMapperWithNoProvider
            ->fromSqlTimestampWithTimeZone($sqlTimestampWithMicroseconds, $this->provider));
        $this->assertEquals($phpTimestampWithMicroseconds, $this->typeMapperWithProvider
            ->fromSqlTimestampWithTimeZone($sqlTimestampWithMicroseconds));
        $phpTimestamp = new DateTime('now');
        $sqlTimestamp = $phpTimestamp->format($this->provider->getTimestampWithoutTimeZoneFormat());
        // We do the flooring of the timestamp because, since PHP 7.1, new DateTime("now") contains microseconds
        $this->assertEquals(
            floor($phpTimestamp->getTimestamp()),
            floor($this->typeMapperWithNoProvider
                ->fromSqlTimestampWithoutTimeZone($sqlTimestamp, $this->provider)->getTimestamp()));
        $this->assertEquals(
            floor($phpTimestamp->getTimestamp()),
            floor($this->typeMapperWithProvider
                ->fromSqlTimestampWithoutTimeZone($sqlTimestamp)->getTimestamp()));
    }

    /**
     * Tests converting from a true SQL boolean
     */
    public function testConvertingFromTrueSqlBoolean()
    {
        $sqlBoolean = 1;
        $this->assertTrue($this->typeMapperWithNoProvider->fromSqlBoolean($sqlBoolean, $this->provider));
        $this->assertTrue($this->typeMapperWithProvider->fromSqlBoolean($sqlBoolean));
    }

    /**
     * Tests converting JSON from null
     */
    public function testConvertingJsonFromNull()
    {
        $this->assertEquals([], $this->typeMapperWithNoProvider->fromSqlJson(null, $this->provider));
        $this->assertEquals([], $this->typeMapperWithProvider->fromSqlJson(null));
    }

    /**
     * Tests converting from SQL JSON
     */
    public function testConvertingJsonFromSql()
    {
        $jsonArray = [
            'foo' => 'bar',
            'baz' => [
                'blah' => 'dave'
            ]
        ];
        $jsonString = json_encode($jsonArray);
        $this->assertEquals($jsonArray, $this->typeMapperWithNoProvider->fromSqlJson($jsonString, $this->provider));
        $this->assertEquals($jsonArray, $this->typeMapperWithProvider->fromSqlJson($jsonString));
    }

    /**
     * Tests converting from a null time with time zone returns null
     */
    public function testConvertingTimeWithTimeZoneFromNullReturnsNull()
    {
        $this->assertNull($this->typeMapperWithNoProvider->fromSqlTimeWithTimeZone(null, $this->provider));
        $this->assertNull($this->typeMapperWithProvider->fromSqlTimeWithTimeZone(null));
    }

    /**
     * Tests converting from a null time without time zone returns null
     */
    public function testConvertingTimeWithoutTimeZoneFromNullReturnsNull()
    {
        $this->assertNull($this->typeMapperWithNoProvider->fromSqlTimeWithoutTimeZone(null, $this->provider));
        $this->assertNull($this->typeMapperWithProvider->fromSqlTimeWithoutTimeZone(null));
    }

    /**
     * Tests converting from a null timestamp with timezone returns null
     */
    public function testConvertingTimestampWithTimeZoneFromNullReturnsNull()
    {
        $this->assertNull($this->typeMapperWithNoProvider->fromSqlTimestampWithTimeZone(null, $this->provider));
        $this->assertNull($this->typeMapperWithProvider->fromSqlTimestampWithTimeZone(null));
    }

    /**
     * Tests converting from a null timestamp without time zone returns null
     */
    public function testConvertingTimestampWithoutTimeZoneFromNullReturnsNull()
    {
        $this->assertNull($this->typeMapperWithNoProvider->fromSqlTimestampWithoutTimeZone(null, $this->provider));
        $this->assertNull($this->typeMapperWithProvider->fromSqlTimestampWithoutTimeZone(null));
    }

    /**
     * Tests converting to a false SQL boolean
     */
    public function testConvertingToFalseSqlBoolean()
    {
        $this->assertEquals(0, $this->typeMapperWithNoProvider->toSqlBoolean(false, $this->provider));
        $this->assertEquals(0, $this->typeMapperWithProvider->toSqlBoolean(false));
    }

    /**
     * Tests converting to an SQL date
     */
    public function testConvertingToSqlDate()
    {
        $date = new DateTime('now');
        $this->assertEquals($date->format($this->provider->getDateFormat()),
            $this->typeMapperWithNoProvider->toSqlDate($date, $this->provider));
        $this->assertEquals($date->format($this->provider->getDateFormat()),
            $this->typeMapperWithProvider->toSqlDate($date));
    }

    /**
     * Tests converting to SQL JSON
     */
    public function testConvertingToSqlJson()
    {
        $jsonArray = [
            'foo' => 'bar',
            'baz' => [
                'blah' => 'dave'
            ]
        ];
        $jsonString = json_encode($jsonArray);
        $this->assertEquals($jsonString, $this->typeMapperWithNoProvider->toSqlJson($jsonArray, $this->provider));
        $this->assertEquals($jsonString, $this->typeMapperWithProvider->toSqlJson($jsonArray));
    }

    /**
     * Tests converting to an SQL time with time zone
     */
    public function testConvertingToSqlTimeWithTimeZone()
    {
        $time = new DateTime('now');
        $this->assertEquals($time->format($this->provider->getTimeWithTimeZoneFormat()),
            $this->typeMapperWithNoProvider->toSqlTimeWithTimeZone($time, $this->provider));
        $this->assertEquals($time->format($this->provider->getTimeWithTimeZoneFormat()),
            $this->typeMapperWithProvider->toSqlTimeWithTimeZone($time));
    }

    /**
     * Tests converting to an SQL time without time zone
     */
    public function testConvertingToSqlTimeWithoutTimeZone()
    {
        $time = new DateTime('now');
        $this->assertEquals($time->format($this->provider->getTimeWithoutTimeZoneFormat()),
            $this->typeMapperWithNoProvider->toSqlTimeWithoutTimeZone($time, $this->provider));
        $this->assertEquals($time->format($this->provider->getTimeWithoutTimeZoneFormat()),
            $this->typeMapperWithProvider->toSqlTimeWithoutTimeZone($time));
    }

    /**
     * Tests converting to an SQL timestamp with time zone
     */
    public function testConvertingToSqlTimestampWithTimeZone()
    {
        $timestamp = new DateTime('now');
        $this->assertEquals($timestamp->format($this->provider->getTimestampWithTimeZoneFormat()),
            $this->typeMapperWithNoProvider->toSqlTimestampWithTimeZone($timestamp, $this->provider));
        $this->assertEquals($timestamp->format($this->provider->getTimestampWithTimeZoneFormat()),
            $this->typeMapperWithProvider->toSqlTimestampWithTimeZone($timestamp));
    }

    /**
     * Tests converting to an SQL timestamp without time zone
     */
    public function testConvertingToSqlTimestampWithoutTimeZone()
    {
        $timestamp = new DateTime('now');
        $this->assertEquals($timestamp->format($this->provider->getTimestampWithoutTimeZoneFormat()),
            $this->typeMapperWithNoProvider->toSqlTimestampWithoutTimeZone($timestamp, $this->provider));
        $this->assertEquals($timestamp->format($this->provider->getTimestampWithoutTimeZoneFormat()),
            $this->typeMapperWithProvider->toSqlTimestampWithoutTimeZone($timestamp));
    }

    /**
     * Tests converting to a true SQL boolean
     */
    public function testConvertingToTrueSqlBoolean()
    {
        $this->assertEquals(1, $this->typeMapperWithNoProvider->toSqlBoolean(true, $this->provider));
        $this->assertEquals(1, $this->typeMapperWithProvider->toSqlBoolean(true));
    }

    /**
     * Tests not setting any providers
     */
    public function testNotSettingAnyProviders()
    {
        $this->expectException(RuntimeException::class);
        $typeMapper = new TypeMapper();
        $typeMapper->toSqlBoolean(true);
    }
}

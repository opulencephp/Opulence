<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\Tests\Providers\Types;

use DateTime;
use Opulence\Databases\Providers\Provider;
use Opulence\Databases\Providers\Types\TypeMapper;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Tests the type mapper class
 */
class TypeMapperTest extends TestCase
{
    private TypeMapper $typeMapperWithNoProvider;
    private TypeMapper $typeMapperWithProvider;
    private Provider $provider;

    protected function setUp(): void
    {
        $this->typeMapperWithNoProvider = new TypeMapper();
        $this->provider = new Provider();
        $this->typeMapperWithProvider = new TypeMapper($this->provider);
    }

    public function testConvertingDateFromNullReturnsNull(): void
    {
        $this->assertNull($this->typeMapperWithNoProvider->fromSqlDate(null, $this->provider));
        $this->assertNull($this->typeMapperWithProvider->fromSqlDate(null));
    }

    public function testConvertingFromFalseSqlBoolean(): void
    {
        $sqlBoolean = 0;
        $this->assertFalse($this->typeMapperWithNoProvider->fromSqlBoolean($sqlBoolean, $this->provider));
        $this->assertFalse($this->typeMapperWithProvider->fromSqlBoolean($sqlBoolean));
    }

    public function testConvertingFromInvalidSqlTimestampWithoutTimeZone(): void
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

    public function testConvertingFromSqlDate(): void
    {
        $phpDate = new DateTime('now');
        $sqlDate = $phpDate->format($this->provider->getDateFormat());
        // Compare the formatted string because testing sometimes leads to the type mapper's date to be one second later
        $this->assertEquals(
            $phpDate->format('Ymd'),
            $this->typeMapperWithNoProvider->fromSqlDate($sqlDate, $this->provider)->format('Ymd')
        );
        // Make sure the hour, minutes, and seconds are zeroed out
        $this->assertEquals(
            '000000',
            $this->typeMapperWithNoProvider->fromSqlDate($sqlDate, $this->provider)->format('His')
        );
        $this->assertEquals(
            $phpDate->format('Ymd'),
            $this->typeMapperWithProvider->fromSqlDate($sqlDate)->format('Ymd')
        );
        // Make sure the hour, minutes, and seconds are zeroed out
        $this->assertEquals('000000', $this->typeMapperWithProvider->fromSqlDate($sqlDate)->format('His'));
    }

    public function testConvertingFromSqlTimeWithTimeZone(): void
    {
        $phpTimeWithMicroseconds = new DateTime('now');
        $sqlTimeWithMicroseconds = $phpTimeWithMicroseconds->format('H:i:s.uP');
        $this->assertEquals($phpTimeWithMicroseconds->getTimestamp(), $this->typeMapperWithNoProvider
            ->fromSqlTimestampWithTimeZone($sqlTimeWithMicroseconds, $this->provider)->getTimestamp());
        $this->assertEquals($phpTimeWithMicroseconds->getTimestamp(), $this->typeMapperWithProvider
            ->fromSqlTimestampWithTimeZone($sqlTimeWithMicroseconds)->getTimestamp());
        $phpTime = new DateTime('now');
        $sqlTime = $phpTime->format($this->provider->getTimeWithTimeZoneFormat());
        $this->assertEquals(
            $phpTime->getTimestamp(),
            $this->typeMapperWithNoProvider->fromSqlTimeWithTimeZone($sqlTime, $this->provider)->getTimestamp()
        );
        $this->assertEquals(
            $phpTime->getTimestamp(),
            $this->typeMapperWithProvider->fromSqlTimeWithTimeZone($sqlTime)->getTimestamp()
        );
    }

    public function testConvertingFromSqlTimeWithoutTimeZone(): void
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
            floor($this->typeMapperWithNoProvider->fromSqlTimeWithoutTimeZone(
                $sqlTime,
                $this->provider
            )->getTimestamp())
        );
        $this->assertEquals(
            floor($phpTime->getTimestamp()),
            floor($this->typeMapperWithProvider->fromSqlTimeWithoutTimeZone($sqlTime)->getTimestamp())
        );
    }

    public function testConvertingFromSqlTimestampWithTimeZone(): void
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
            floor($this->typeMapperWithNoProvider->fromSqlTimestampWithTimeZone(
                $sqlTimestamp,
                $this->provider
            )->getTimestamp())
        );
        $this->assertEquals(
            floor($phpTimestamp->getTimestamp()),
            floor($this->typeMapperWithProvider->fromSqlTimestampWithTimeZone($sqlTimestamp)->getTimestamp())
        );
    }

    public function testConvertingFromSqlTimestampWithoutTimeZone(): void
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
                ->fromSqlTimestampWithoutTimeZone($sqlTimestamp, $this->provider)->getTimestamp())
        );
        $this->assertEquals(
            floor($phpTimestamp->getTimestamp()),
            floor($this->typeMapperWithProvider
                ->fromSqlTimestampWithoutTimeZone($sqlTimestamp)->getTimestamp())
        );
    }

    public function testConvertingFromTrueSqlBoolean(): void
    {
        $sqlBoolean = 1;
        $this->assertTrue($this->typeMapperWithNoProvider->fromSqlBoolean($sqlBoolean, $this->provider));
        $this->assertTrue($this->typeMapperWithProvider->fromSqlBoolean($sqlBoolean));
    }

    public function testConvertingJsonFromNull(): void
    {
        $this->assertEquals([], $this->typeMapperWithNoProvider->fromSqlJson(null, $this->provider));
        $this->assertEquals([], $this->typeMapperWithProvider->fromSqlJson(null));
    }

    public function testConvertingJsonFromSql(): void
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

    public function testConvertingTimeWithTimeZoneFromNullReturnsNull(): void
    {
        $this->assertNull($this->typeMapperWithNoProvider->fromSqlTimeWithTimeZone(null, $this->provider));
        $this->assertNull($this->typeMapperWithProvider->fromSqlTimeWithTimeZone(null));
    }

    public function testConvertingTimeWithoutTimeZoneFromNullReturnsNull(): void
    {
        $this->assertNull($this->typeMapperWithNoProvider->fromSqlTimeWithoutTimeZone(null, $this->provider));
        $this->assertNull($this->typeMapperWithProvider->fromSqlTimeWithoutTimeZone(null));
    }

    public function testConvertingTimestampWithTimeZoneFromNullReturnsNull(): void
    {
        $this->assertNull($this->typeMapperWithNoProvider->fromSqlTimestampWithTimeZone(null, $this->provider));
        $this->assertNull($this->typeMapperWithProvider->fromSqlTimestampWithTimeZone(null));
    }

    public function testConvertingTimestampWithoutTimeZoneFromNullReturnsNull(): void
    {
        $this->assertNull($this->typeMapperWithNoProvider->fromSqlTimestampWithoutTimeZone(null, $this->provider));
        $this->assertNull($this->typeMapperWithProvider->fromSqlTimestampWithoutTimeZone(null));
    }

    public function testConvertingToFalseSqlBoolean(): void
    {
        $this->assertEquals(0, $this->typeMapperWithNoProvider->toSqlBoolean(false, $this->provider));
        $this->assertEquals(0, $this->typeMapperWithProvider->toSqlBoolean(false));
    }

    public function testConvertingToSqlDate(): void
    {
        $date = new DateTime('now');
        $this->assertEquals(
            $date->format($this->provider->getDateFormat()),
            $this->typeMapperWithNoProvider->toSqlDate($date, $this->provider)
        );
        $this->assertEquals(
            $date->format($this->provider->getDateFormat()),
            $this->typeMapperWithProvider->toSqlDate($date)
        );
    }

    public function testConvertingToSqlJson(): void
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

    public function testConvertingToSqlTimeWithTimeZone(): void
    {
        $time = new DateTime('now');
        $this->assertEquals(
            $time->format($this->provider->getTimeWithTimeZoneFormat()),
            $this->typeMapperWithNoProvider->toSqlTimeWithTimeZone($time, $this->provider)
        );
        $this->assertEquals(
            $time->format($this->provider->getTimeWithTimeZoneFormat()),
            $this->typeMapperWithProvider->toSqlTimeWithTimeZone($time)
        );
    }

    public function testConvertingToSqlTimeWithoutTimeZone(): void
    {
        $time = new DateTime('now');
        $this->assertEquals(
            $time->format($this->provider->getTimeWithoutTimeZoneFormat()),
            $this->typeMapperWithNoProvider->toSqlTimeWithoutTimeZone($time, $this->provider)
        );
        $this->assertEquals(
            $time->format($this->provider->getTimeWithoutTimeZoneFormat()),
            $this->typeMapperWithProvider->toSqlTimeWithoutTimeZone($time)
        );
    }

    public function testConvertingToSqlTimestampWithTimeZone(): void
    {
        $timestamp = new DateTime('now');
        $this->assertEquals(
            $timestamp->format($this->provider->getTimestampWithTimeZoneFormat()),
            $this->typeMapperWithNoProvider->toSqlTimestampWithTimeZone($timestamp, $this->provider)
        );
        $this->assertEquals(
            $timestamp->format($this->provider->getTimestampWithTimeZoneFormat()),
            $this->typeMapperWithProvider->toSqlTimestampWithTimeZone($timestamp)
        );
    }

    public function testConvertingToSqlTimestampWithoutTimeZone(): void
    {
        $timestamp = new DateTime('now');
        $this->assertEquals(
            $timestamp->format($this->provider->getTimestampWithoutTimeZoneFormat()),
            $this->typeMapperWithNoProvider->toSqlTimestampWithoutTimeZone($timestamp, $this->provider)
        );
        $this->assertEquals(
            $timestamp->format($this->provider->getTimestampWithoutTimeZoneFormat()),
            $this->typeMapperWithProvider->toSqlTimestampWithoutTimeZone($timestamp)
        );
    }

    public function testConvertingToTrueSqlBoolean(): void
    {
        $this->assertEquals(1, $this->typeMapperWithNoProvider->toSqlBoolean(true, $this->provider));
        $this->assertEquals(1, $this->typeMapperWithProvider->toSqlBoolean(true));
    }

    public function testNotSettingAnyProviders(): void
    {
        $this->expectException(RuntimeException::class);
        $typeMapper = new TypeMapper();
        $typeMapper->toSqlBoolean(true);
    }
}

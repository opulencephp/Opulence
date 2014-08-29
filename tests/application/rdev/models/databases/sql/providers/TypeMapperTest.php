<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the type mapper class
 */
namespace RDev\Models\Databases\SQL\Providers;

class TypeMapperTest extends \PHPUnit_Framework_TestCase
{
    /** @var TypeMapper The type mapper to use for tests */
    private $typMapper = null;
    /** @var Provider The provider to use for tests */
    private $provider = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->typMapper = new TypeMapper();
        $this->provider = new Provider();
    }

    /**
     * Tests converting from a null date returns null
     */
    public function testConvertingDateFromNullReturnsNull()
    {
        $this->assertNull($this->typMapper->fromSQLDate($this->provider, null));
    }

    /**
     * Tests converting from a false SQL boolean
     */
    public function testConvertingFromFalseSQLBoolean()
    {
        $sqlBoolean = $this->provider->getFalseBooleanFormat();
        $this->assertSame(false, $this->typMapper->fromSQLBoolean($this->provider, $sqlBoolean));
    }

    /**
     * Tests converting from an SQL date
     */
    public function testConvertingFromSQLDate()
    {
        $sqlDate = \DateTime::createFromFormat($this->provider->getDateFormat(), "now", new \DateTimeZone("UTC"));
        $this->assertEquals($sqlDate, $this->typMapper->fromSQLDate($this->provider, $sqlDate));
    }

    /**
     * Tests converting from an SQL timestamp without time zone
     */
    public function testConvertingFromSQLTimeStampWithoutTimeZone()
    {
        $sqlTimestamp = \DateTime::createFromFormat($this->provider->getTimestampWithoutTimeZoneFormat(), "now",
            new \DateTimeZone("UTC"));
        $this->assertEquals($sqlTimestamp, $this->typMapper->fromSQLTimestampWithOutTimeZone($this->provider, $sqlTimestamp));
    }

    /**
     * Tests converting from an SQL time with time zone
     */
    public function testConvertingFromSQLTimeWithTimeZone()
    {
        $sqlTime = \DateTime::createFromFormat($this->provider->getTimeWithTimeZoneFormat(), "now", new \DateTimeZone("UTC"));
        $this->assertEquals($sqlTime, $this->typMapper->fromSQLTimeWithoutTimeZone($this->provider, $sqlTime));
    }

    /**
     * Tests converting from an SQL time without time zone
     */
    public function testConvertingFromSQLTimeWithoutTimeZone()
    {
        $sqlTime = \DateTime::createFromFormat($this->provider->getTimeWithoutTimeZoneFormat(), "now", new \DateTimeZone("UTC"));
        $this->assertEquals($sqlTime, $this->typMapper->fromSQLTimeWithoutTimeZone($this->provider, $sqlTime));
    }

    /**
     * Tests converting from an SQL timestamp with time zone
     */
    public function testConvertingFromSQLTimestampWithTimeZone()
    {
        $sqlTimestamp = \DateTime::createFromFormat($this->provider->getTimestampWithTimeZoneFormat(), "now",
            new \DateTimeZone("UTC"));
        $this->assertEquals($sqlTimestamp, $this->typMapper->fromSQLTimestampWithTimeZone($this->provider, $sqlTimestamp));
    }

    /**
     * Tests converting from a true SQL boolean
     */
    public function testConvertingFromTrueSQLBoolean()
    {
        $sqlBoolean = $this->provider->getTrueBooleanFormat();
        $this->assertSame(true, $this->typMapper->fromSQLBoolean($this->provider, $sqlBoolean));
    }

    /**
     * Tests converting from a null time with time zone returns null
     */
    public function testConvertingTimeWithTimeZoneFromNullReturnsNull()
    {
        $this->assertNull($this->typMapper->fromSQLTimeWithTimeZone($this->provider, null));
    }

    /**
     * Tests converting from a null time without time zone returns null
     */
    public function testConvertingTimeWithoutTimeZoneFromNullReturnsNull()
    {
        $this->assertNull($this->typMapper->fromSQLTimeWithoutTimeZone($this->provider, null));
    }

    /**
     * Tests converting from a null timestamp with timezone returns null
     */
    public function testConvertingTimestampWithTimeZoneFromNullReturnsNull()
    {
        $this->assertNull($this->typMapper->fromSQLTimestampWithTimeZone($this->provider, null));
    }

    /**
     * Tests converting from a null timestamp without time zone returns null
     */
    public function testConvertingTimestampWithoutTimeZoneFromNullReturnsNull()
    {
        $this->assertNull($this->typMapper->fromSQLTimestampWithOutTimeZone($this->provider, null));
    }

    /**
     * Tests converting to a false SQL boolean
     */
    public function testConvertingToFalseSQLBoolean()
    {
        $this->assertEquals($this->provider->getFalseBooleanFormat(), $this->typMapper->toSQLBoolean($this->provider, false));
    }

    /**
     * Tests converting to an SQL date
     */
    public function testConvertingToSQLDate()
    {
        $date = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->assertEquals($date->format($this->provider->getDateFormat()), $this->typMapper->toSQLDate($this->provider, $date));
    }

    /**
     * Tests converting to an SQL time with time zone
     */
    public function testConvertingToSQLTimeWithTimeZone()
    {
        $time = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->assertEquals($time->format($this->provider->getTimeWithTimeZoneFormat()), $this->typMapper->toSQLTimeWithTimeZone($this->provider, $time));
    }

    /**
     * Tests converting to an SQL time without time zone
     */
    public function testConvertingToSQLTimeWithoutTimeZone()
    {
        $time = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->assertEquals($time->format($this->provider->getTimeWithoutTimeZoneFormat()), $this->typMapper->toSQLTimeWithoutTimeZone($this->provider, $time));
    }

    /**
     * Tests converting to an SQL timestamp with time zone
     */
    public function testConvertingToSQLTimestampWithTimeZone()
    {
        $timestamp = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->assertEquals($timestamp->format($this->provider->getTimestampWithTimeZoneFormat()),
            $this->typMapper->toSQLTimestampWithTimeZone($this->provider, $timestamp));
    }

    /**
     * Tests converting to an SQL timestamp without time zone
     */
    public function testConvertingToSQLTimestampWithoutTimeZone()
    {
        $timestamp = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->assertEquals($timestamp->format($this->provider->getTimestampWithoutTimeZoneFormat()),
            $this->typMapper->toSQLTimestampWithoutTimeZone($this->provider, $timestamp));
    }

    /**
     * Tests converting to a true SQL boolean
     */
    public function testConvertingToTrueSQLBoolean()
    {
        $this->assertEquals($this->provider->getTrueBooleanFormat(), $this->typMapper->toSQLBoolean($this->provider, true));
    }
} 
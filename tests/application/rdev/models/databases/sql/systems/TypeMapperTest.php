<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the type mapper class
 */
namespace RDev\Models\Databases\SQL\Systems;

class TypeMapperTest extends \PHPUnit_Framework_TestCase
{
    /** @var TypeMapper The type mapper to use for tests */
    private $typMapper = null;
    /** @var System The system to use for tests */
    private $system = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->typMapper = new TypeMapper();
        $this->system = new System();
    }

    /**
     * Tests converting from a null date returns null
     */
    public function testConvertingDateFromNullReturnsNull()
    {
        $this->assertNull($this->typMapper->fromSQLDate($this->system, null));
    }

    /**
     * Tests converting from an SQL date
     */
    public function testConvertingFromSQLDate()
    {
        $sqlDate = \DateTime::createFromFormat($this->system->getDateFormat(), "now", new \DateTimeZone("UTC"));
        $this->assertEquals($sqlDate, $this->typMapper->fromSQLDate($this->system, $sqlDate));
    }

    /**
     * Tests converting from an SQL time
     */
    public function testConvertingFromSQLTime()
    {
        $sqlTime = \DateTime::createFromFormat($this->system->getTimeFormat(), "now", new \DateTimeZone("UTC"));
        $this->assertEquals($sqlTime, $this->typMapper->fromSQLTime($this->system, $sqlTime));
    }

    /**
     * Tests converting from an SQL timestamp without time zone
     */
    public function testConvertingFromSQLTimeStampWithoutTimeZone()
    {
        $sqlTimestamp = \DateTime::createFromFormat($this->system->getTimestampWithoutTimeZoneFormat(), "now",
            new \DateTimeZone("UTC"));
        $this->assertEquals($sqlTimestamp, $this->typMapper->fromSQLTimestampWithOutTimeZone($this->system, $sqlTimestamp));
    }

    /**
     * Tests converting from an SQL timestamp with time zone
     */
    public function testConvertingFromSQLTimestampWithTimeZone()
    {
        $sqlTimestamp = \DateTime::createFromFormat($this->system->getTimestampWithTimeZoneFormat(), "now",
            new \DateTimeZone("UTC"));
        $this->assertEquals($sqlTimestamp, $this->typMapper->fromSQLTimestampWithTimeZone($this->system, $sqlTimestamp));
    }

    /**
     * Tests converting from a null time returns null
     */
    public function testConvertingTimeFromNullReturnsNull()
    {
        $this->assertNull($this->typMapper->fromSQLTime($this->system, null));
    }

    /**
     * Tests converting from a null timestamp with timezone returns null
     */
    public function testConvertingTimestampWithTimeZoneFromNullReturnsNull()
    {
        $this->assertNull($this->typMapper->fromSQLTimestampWithTimeZone($this->system, null));
    }

    /**
     * Tests converting from a null timestamp without time zone returns null
     */
    public function testConvertingTimestampWithoutTimeZoneFromNullReturnsNull()
    {
        $this->assertNull($this->typMapper->fromSQLTimestampWithOutTimeZone($this->system, null));
    }

    /**
     * Tests converting to an SQL date
     */
    public function testConvertingToSQLDate()
    {
        $date = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->assertEquals($date->format($this->system->getDateFormat()), $this->typMapper->toSQLDate($this->system, $date));
    }

    /**
     * Tests converting to an SQL time
     */
    public function testConvertingToSQLTime()
    {
        $time = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->assertEquals($time->format($this->system->getTimeFormat()), $this->typMapper->toSQLTime($this->system, $time));
    }

    /**
     * Tests converting to an SQL timestamp with time zone
     */
    public function testConvertingToSQLTimestampWithTimeZone()
    {
        $timestamp = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->assertEquals($timestamp->format($this->system->getTimestampWithTimeZoneFormat()),
            $this->typMapper->toSQLTimestampWithTimeZone($this->system, $timestamp));
    }

    /**
     * Tests converting to an SQL timestamp without time zone
     */
    public function testConvertingToSQLTimestampWithoutTimeZone()
    {
        $timestamp = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->assertEquals($timestamp->format($this->system->getTimestampWithoutTimeZoneFormat()),
            $this->typMapper->toSQLTimestampWithoutTimeZone($this->system, $timestamp));
    }
} 
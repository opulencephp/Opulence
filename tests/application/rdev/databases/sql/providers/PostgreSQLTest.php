<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the PostgreSQL provider class
 */
namespace RDev\Databases\SQL\Providers;

class PostgreSQLTest extends \PHPUnit_Framework_TestCase
{
    /** @var PostgreSQL The provider to use for tests */
    private $postgreSQL = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->postgreSQL = new PostgreSQL();
    }

    /**
     * Tests converting from a null SQL boolean
     */
    public function testConvertingFromNullSQLBoolean()
    {
        $this->assertNull($this->postgreSQL->convertFromSQLBoolean(null));
    }

    /**
     * Tests converting from an SQL boolean
     */
    public function testConvertingFromSQLBoolean()
    {
        $trueBooleanValues = [
            't',
            'true',
            '1',
            'y',
            'yes',
            'on'
        ];
        $falseBooleanValues = [
            'f',
            'false',
            '0',
            'n',
            'no',
            'off'
        ];

        foreach($trueBooleanValues as $value)
        {
            $this->assertTrue($this->postgreSQL->convertFromSQLBoolean($value));
        }

        foreach($falseBooleanValues as $value)
        {
            $this->assertFalse($this->postgreSQL->convertFromSQLBoolean($value));
        }
    }

    /**
     * Tests converting a non-boolean value to an SQL boolean
     */
    public function testConvertingNonBooleanValueToSQLBoolean()
    {
        $this->assertEquals("foo", $this->postgreSQL->convertToSQLBoolean("foo"));
    }

    /**
     * Tests converting to an SQL boolean
     */
    public function testConvertingToSQLBoolean()
    {
        $this->assertEquals('t', $this->postgreSQL->convertToSQLBoolean(true));
        $this->assertEquals('f', $this->postgreSQL->convertToSQLBoolean(false));
    }
} 
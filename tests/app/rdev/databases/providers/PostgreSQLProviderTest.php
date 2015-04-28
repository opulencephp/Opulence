<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the PostgreSQL provider class
 */
namespace RDev\Databases\Providers;

class PostgreSQLProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var PostgreSQLProvider The provider to use for tests */
    private $provider = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->provider = new PostgreSQLProvider();
    }

    /**
     * Tests converting from a null SQL boolean
     */
    public function testConvertingFromNullSQLBoolean()
    {
        $this->assertNull($this->provider->convertFromSQLBoolean(null));
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
            $this->assertTrue($this->provider->convertFromSQLBoolean($value));
        }

        foreach($falseBooleanValues as $value)
        {
            $this->assertFalse($this->provider->convertFromSQLBoolean($value));
        }
    }

    /**
     * Tests converting a non-boolean value to an SQL boolean
     */
    public function testConvertingNonBooleanValueToSQLBoolean()
    {
        $this->assertEquals("foo", $this->provider->convertToSQLBoolean("foo"));
    }

    /**
     * Tests converting to an SQL boolean
     */
    public function testConvertingToSQLBoolean()
    {
        $this->assertEquals('t', $this->provider->convertToSQLBoolean(true));
        $this->assertEquals('f', $this->provider->convertToSQLBoolean(false));
    }
} 
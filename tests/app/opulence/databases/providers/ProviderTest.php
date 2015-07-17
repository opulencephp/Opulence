<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the provider class
 */
namespace Opulence\Databases\Providers;

class ProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var Provider The provider to use for tests */
    private $provider = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->provider = new Provider();
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
        $this->assertTrue($this->provider->convertFromSQLBoolean("1"));
        $this->assertFalse($this->provider->convertFromSQLBoolean("0"));
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
        $this->assertEquals(1, $this->provider->convertToSQLBoolean(true));
        $this->assertEquals(0, $this->provider->convertToSQLBoolean(false));
    }
} 
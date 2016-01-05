<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Databases\Providers;

/**
 * Tests the provider class
 */
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
    public function testConvertingFromNullSqlBoolean()
    {
        $this->assertNull($this->provider->convertFromSqlBoolean(null));
    }

    /**
     * Tests converting from an SQL boolean
     */
    public function testConvertingFromSqlBoolean()
    {
        $this->assertTrue($this->provider->convertFromSqlBoolean("1"));
        $this->assertFalse($this->provider->convertFromSqlBoolean("0"));
    }

    /**
     * Tests converting a non-boolean value to an SQL boolean
     */
    public function testConvertingNonBooleanValueToSqlBoolean()
    {
        $this->assertEquals("foo", $this->provider->convertToSqlBoolean("foo"));
    }

    /**
     * Tests converting to an SQL boolean
     */
    public function testConvertingToSqlBoolean()
    {
        $this->assertEquals(1, $this->provider->convertToSqlBoolean(true));
        $this->assertEquals(0, $this->provider->convertToSqlBoolean(false));
    }
} 
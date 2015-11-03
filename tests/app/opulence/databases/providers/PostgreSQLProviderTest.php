<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Databases\Providers;

/**
 * Tests the PostgreSQL provider class
 */
class PostgreSqlProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var PostgreSqlProvider The provider to use for tests */
    private $provider = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->provider = new PostgreSqlProvider();
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

        foreach ($trueBooleanValues as $value) {
            $this->assertTrue($this->provider->convertFromSqlBoolean($value));
        }

        foreach ($falseBooleanValues as $value) {
            $this->assertFalse($this->provider->convertFromSqlBoolean($value));
        }
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
        $this->assertEquals('t', $this->provider->convertToSqlBoolean(true));
        $this->assertEquals('f', $this->provider->convertToSqlBoolean(false));
    }
} 
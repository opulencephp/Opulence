<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\Tests\Providers;

use Opulence\Databases\Providers\Provider;

/**
 * Tests the provider class
 */
class ProviderTest extends \PHPUnit\Framework\TestCase
{
    /** @var Provider The provider to use for tests */
    private $provider;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->provider = new Provider();
    }

    /**
     * Tests converting from a null SQL boolean
     */
    public function testConvertingFromNullSqlBoolean(): void
    {
        $this->assertNull($this->provider->convertFromSqlBoolean(null));
    }

    /**
     * Tests converting from an SQL boolean
     */
    public function testConvertingFromSqlBoolean(): void
    {
        $this->assertTrue($this->provider->convertFromSqlBoolean('1'));
        $this->assertFalse($this->provider->convertFromSqlBoolean('0'));
    }

    /**
     * Tests converting to an SQL boolean
     */
    public function testConvertingToSqlBoolean(): void
    {
        $this->assertEquals(1, $this->provider->convertToSqlBoolean(true));
        $this->assertEquals(0, $this->provider->convertToSqlBoolean(false));
    }
}

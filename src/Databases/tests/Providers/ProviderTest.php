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
use PHPUnit\Framework\TestCase;

/**
 * Tests the provider class
 */
class ProviderTest extends TestCase
{
    private Provider $provider;

    protected function setUp(): void
    {
        $this->provider = new Provider();
    }

    public function testConvertingFromNullSqlBoolean(): void
    {
        $this->assertNull($this->provider->convertFromSqlBoolean(null));
    }

    public function testConvertingFromSqlBoolean(): void
    {
        $this->assertTrue($this->provider->convertFromSqlBoolean('1'));
        $this->assertFalse($this->provider->convertFromSqlBoolean('0'));
    }

    public function testConvertingToSqlBoolean(): void
    {
        $this->assertEquals(1, $this->provider->convertToSqlBoolean(true));
        $this->assertEquals(0, $this->provider->convertToSqlBoolean(false));
    }
}

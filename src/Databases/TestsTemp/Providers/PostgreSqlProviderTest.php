<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\TestsTemp\Providers;

use Opulence\Databases\Providers\PostgreSqlProvider;

/**
 * Tests the PostgreSQL provider class
 */
class PostgreSqlProviderTest extends \PHPUnit\Framework\TestCase
{
    private PostgreSqlProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new PostgreSqlProvider();
    }

    public function testConvertingFromNullSqlBoolean(): void
    {
        $this->assertNull($this->provider->convertFromSqlBoolean(null));
    }

    public function testConvertingFromSqlBoolean(): void
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

    public function testConvertingToSqlBoolean(): void
    {
        $this->assertEquals('t', $this->provider->convertToSqlBoolean(true));
        $this->assertEquals('f', $this->provider->convertToSqlBoolean(false));
    }
}

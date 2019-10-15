<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Tests\Configuration;

use Opulence\Framework\Configuration\Config;
use PHPUnit\Framework\TestCase;

/**
 * Tests the config reader
 */
class ConfigTest extends TestCase
{
    /**
     * Tests getting a non-existent value
     */
    public function testGettingNonExistentValue(): void
    {
        $this->assertNull(Config::get('foo', 'bar'));
        $this->assertEquals('baz', Config::get('foo', 'bar', 'baz'));
        $this->assertFalse(Config::has('foo', 'bar'));
    }

    public function testSettingCategory(): void
    {
        Config::setCategory('foo', ['bar' => 'baz']);
        $this->assertEquals('baz', Config::get('foo', 'bar'));
        $this->assertTrue(Config::has('foo', 'bar'));
        Config::setCategory('foo', ['dave' => 'young']);
        $this->assertEquals('young', Config::get('foo', 'dave'));
        $this->assertTrue(Config::has('foo', 'dave'));
    }

    public function testSettingSingleSetting(): void
    {
        Config::set('foo', 'bar', 'baz');
        $this->assertEquals('baz', Config::get('foo', 'bar'));
        $this->assertTrue(Config::has('foo', 'bar'));
        Config::set('foo', 'bar', 'blah');
        $this->assertEquals('blah', Config::get('foo', 'bar'));
        $this->assertTrue(Config::has('foo', 'bar'));
    }
}

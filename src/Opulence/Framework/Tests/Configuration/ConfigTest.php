<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Tests\Configuration;

use Opulence\Framework\Configuration\Config;

/**
 * Tests the config reader
 */
class ConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getting a non-existent value
     */
    public function testGettingNonExistentValue()
    {
        $this->assertNull(Config::get('foo', 'bar'));
        $this->assertEquals('baz', Config::get('foo', 'bar', 'baz'));
        $this->assertFalse(Config::has('foo', 'bar'));
    }

    /**
     * Tests setting many settings by category
     */
    public function testSettingCategory()
    {
        Config::setCategory('foo', ['bar' => 'baz']);
        $this->assertEquals('baz', Config::get('foo', 'bar'));
        $this->assertTrue(Config::has('foo', 'bar'));
        Config::setCategory('foo', ['dave' => 'young']);
        $this->assertEquals('young', Config::get('foo', 'dave'));
        $this->assertTrue(Config::has('foo', 'dave'));
    }

    /**
     * Tests setting a single setting
     */
    public function testSettingSingleSetting()
    {
        Config::set('foo', 'bar', 'baz');
        $this->assertEquals('baz', Config::get('foo', 'bar'));
        $this->assertTrue(Config::has('foo', 'bar'));
        Config::set('foo', 'bar', 'blah');
        $this->assertEquals('blah', Config::get('foo', 'bar'));
        $this->assertTrue(Config::has('foo', 'bar'));
    }
}

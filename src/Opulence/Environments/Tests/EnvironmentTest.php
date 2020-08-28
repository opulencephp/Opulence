<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Environments\Tests;

use Opulence\Environments\Environment;

/**
 * Tests the environment
 */
class EnvironmentTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getting a non-existent variable
     */
    public function testGettingNonExistentVariable()
    {
        $this->assertSame('bar', Environment::getVar('foo', 'bar'));
    }

    /**
     * Tests getting a variable
     */
    public function testGettingVariable()
    {
        Environment::setVar('baz', 'blah');
        $this->assertSame('blah', getenv('baz'));
        $this->assertSame('blah', $_ENV['baz']);
        $this->assertSame('blah', $_SERVER['baz']);
    }

    /**
     * Tests checking if the application is running in a console
     */
    public function testIsRunningInConsole()
    {
        $this->assertEquals(Environment::isRunningInConsole(), php_sapi_name() === 'cli');
    }

    /**
     * Tests setting a variable
     */
    public function testSettingVariable()
    {
        Environment::setVar('foo', 'bar');
        $this->assertSame('bar', getenv('foo'));
        $this->assertSame('bar', $_ENV['foo']);
        $this->assertSame('bar', $_SERVER['foo']);
    }

    /**
     * Tests setting a variable in environment global array()
     */
    public function testSettingVariableInEnvironmentGlobalArray()
    {
        $_ENV['bar'] = 'baz';
        $this->assertSame('baz', Environment::getVar('bar'));
    }

    /**
     * Tests setting a variable in putenv()
     */
    public function testSettingVariableInPutenv()
    {
        putenv('bar=baz');
        $this->assertSame('baz', Environment::getVar('bar'));
    }

    /**
     * Tests setting a variable in server global array()
     */
    public function testSettingVariableInServerGlobalArray()
    {
        $_SERVER['bar'] = 'baz';
        $this->assertSame('baz', Environment::getVar('bar'));
    }

    /**
     * Tests that environment variables are not overwritten
     */
    public function testVariablesNotOverwritten()
    {
        Environment::setVar('foo', 'bar');
        Environment::setVar('foo', 'baz');
        $this->assertSame('bar', Environment::getVar('foo'));
        $this->assertSame('bar', getenv('foo'));
    }
}

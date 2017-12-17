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
    public function testGettingNonExistentVariable() : void
    {
        $this->assertEquals('bar', Environment::getVar('foo', 'bar'));
    }

    /**
     * Tests getting a variable
     */
    public function testGettingVariable() : void
    {
        Environment::setVar('baz', 'blah');
        $this->assertEquals('blah', getenv('baz'));
        $this->assertEquals('blah', $_ENV['baz']);
        $this->assertEquals('blah', $_SERVER['baz']);
    }

    /**
     * Tests checking if the application is running in a console
     */
    public function testIsRunningInConsole() : void
    {
        $this->assertEquals(Environment::isRunningInConsole(), php_sapi_name() === 'cli');
    }

    /**
     * Tests setting a variable
     */
    public function testSettingVariable() : void
    {
        Environment::setVar('foo', 'bar');
        $this->assertEquals('bar', getenv('foo'));
        $this->assertEquals('bar', $_ENV['foo']);
        $this->assertEquals('bar', $_SERVER['foo']);
    }

    /**
     * Tests setting a variable in environment global array()
     */
    public function testSettingVariableInEnvironmentGlobalArray() : void
    {
        $_ENV['bar'] = 'baz';
        $this->assertEquals('baz', Environment::getVar('bar'));
    }

    /**
     * Tests setting a variable in putenv()
     */
    public function testSettingVariableInPutenv() : void
    {
        putenv('bar=baz');
        $this->assertEquals('baz', Environment::getVar('bar'));
    }

    /**
     * Tests setting a variable in server global array()
     */
    public function testSettingVariableInServerGlobalArray() : void
    {
        $_SERVER['bar'] = 'baz';
        $this->assertEquals('baz', Environment::getVar('bar'));
    }

    /**
     * Tests that environment variables are not overwritten
     */
    public function testVariablesNotOverwritten() : void
    {
        Environment::setVar('foo', 'bar');
        Environment::setVar('foo', 'baz');
        $this->assertEquals('bar', Environment::getVar('foo'));
        $this->assertEquals('bar', getenv('foo'));
    }
}

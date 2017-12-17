<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Http\Tests;

use Opulence\Http\Collection;

/**
 * Tests the request collection
 */
class CollectionTest extends \PHPUnit\Framework\TestCase
{
    /** @var Collection The parameters to use in tests */
    private $parameters = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->parameters = new Collection([]);
    }

    /**
     * Tests adding a parameter
     */
    public function testAdding() : void
    {
        $this->parameters->add('foo', 'bar');
        $this->assertEquals('bar', $this->parameters->get('foo'));
    }

    /**
     * Tests checking if an offset exists
     */
    public function testCheckingOffsetExists() : void
    {
        $this->parameters['foo'] = 'bar';
        $this->assertTrue(isset($this->parameters['foo']));
    }

    /**
     * Tests counting
     */
    public function testCount() : void
    {
        $this->parameters->add('foo', 'bar');
        $this->assertEquals(1, $this->parameters->count());
        $this->parameters->add('bar', 'foo');
        $this->assertEquals(2, $this->parameters->count());
    }

    /**
     * Tests exchanging the array
     */
    public function testExchangingArray() : void
    {
        $this->parameters->add('foo', 'bar');
        $this->assertEquals(['foo' => 'bar'], $this->parameters->exchangeArray(['bar' => 'foo']));
        $this->assertEquals(['bar' => 'foo'], $this->parameters->getAll());
    }

    /**
     * Tests getting a parameter
     */
    public function testGetting() : void
    {
        $this->parameters->add('foo', 'bar');
        $this->assertEquals('bar', $this->parameters->get('foo'));
    }

    /**
     * Tests getting an absent variable with a default
     */
    public function testGettingAbsentVariableWithDefault() : void
    {
        $this->assertEquals('blah', $this->parameters->get('does not exist', 'blah'));
    }

    /**
     * Tests getting an absent variable with no default
     */
    public function testGettingAbsentVariableWithNoDefault() : void
    {
        $this->assertNull($this->parameters->get('does not exist'));
    }

    /**
     * Tests getting all the parameters
     */
    public function testGettingAll() : void
    {
        $this->parameters->add('foo', 'bar');
        $this->parameters->add('bar', 'foo');
        $this->assertEquals([
            'foo' => 'bar',
            'bar' => 'foo'
        ], $this->parameters->getAll());
    }

    /**
     * Tests getting as array
     */
    public function testGettingAsArray() : void
    {
        $this->parameters->add('foo', 'bar');
        $this->assertEquals('bar', $this->parameters['foo']);
    }

    /**
     * Tests whether the parameters has a certain parameter
     */
    public function testHas() : void
    {
        $this->assertFalse($this->parameters->has('foo'));
        $this->parameters->add('foo', 'bar');
        $this->assertTrue($this->parameters->has('foo'));
    }

    /**
     * Tests passing parameters through the constructor
     */
    public function testPassingParametersInConstructor() : void
    {
        $parametersArray = ['foo' => 'bar', 'bar' => 'foo'];
        $parameters = new Collection($parametersArray);
        $this->assertEquals($parametersArray, $parameters->getAll());
    }

    /**
     * Tests removing a parameter
     */
    public function testRemove() : void
    {
        $this->parameters->add('foo', 'bar');
        $this->parameters->remove('foo');
        $this->assertNull($this->parameters->get('foo'));
    }

    /**
     * Tests setting a parameter
     */
    public function testSetting() : void
    {
        $this->parameters->set('foo', 'bar');
        $this->assertEquals('bar', $this->parameters->get('foo'));
    }

    /**
     * Tests setting an item
     */
    public function testSettingItem() : void
    {
        $this->parameters['foo'] = 'bar';
        $this->assertEquals('bar', $this->parameters['foo']);
    }

    /**
     * Tests unsetting a parameter
     */
    public function testUnsetting() : void
    {
        $this->parameters['foo'] = 'bar';
        unset($this->parameters['foo']);
        $this->assertNull($this->parameters->get('foo'));
    }

    /**
     * Tests unsetting an item
     */
    public function testUnsettingItem() : void
    {
        $this->parameters->add('foo', 'bar');
        unset($this->parameters['foo']);
        $this->assertEquals(null, $this->parameters['foo']);
    }
}

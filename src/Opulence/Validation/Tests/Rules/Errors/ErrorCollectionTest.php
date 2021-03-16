<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Validation\Tests\Rules\Errors;

use Opulence\Validation\Rules\Errors\ErrorCollection;

/**
 * Tests the error collection
 */
class ErrorCollectionTest extends \PHPUnit\Framework\TestCase
{
    /** @var ErrorCollection The parameters to use in tests */
    private $collection = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->collection = new ErrorCollection();
    }

    /**
     * Tests adding a parameter
     */
    public function testAdding()
    {
        $this->collection->add('foo', 'bar');
        $this->assertEquals('bar', $this->collection->get('foo'));
    }

    /**
     * Tests checking if an offset exists
     */
    public function testCheckingOffsetExists()
    {
        $this->collection['foo'] = 'bar';
        $this->assertTrue(isset($this->collection['foo']));
    }

    /**
     * Tests counting
     */
    public function testCount()
    {
        $this->collection->add('foo', 'bar');
        $this->assertEquals(1, $this->collection->count());
        $this->collection->add('bar', 'foo');
        $this->assertEquals(2, $this->collection->count());
    }

    /**
     * Tests exchanging the array
     */
    public function testExchangingArray()
    {
        $this->collection->add('foo', 'bar');
        $this->assertEquals(['foo' => 'bar'], $this->collection->exchangeArray(['bar' => 'foo']));
        $this->assertEquals(['bar' => 'foo'], $this->collection->getAll());
    }

    /**
     * Tests getting a parameter
     */
    public function testGetting()
    {
        $this->collection->add('foo', 'bar');
        $this->assertEquals('bar', $this->collection->get('foo'));
    }

    /**
     * Tests getting an absent variable with a default
     */
    public function testGettingAbsentVariableWithDefault()
    {
        $this->assertEquals('blah', $this->collection->get('does not exist', 'blah'));
    }

    /**
     * Tests getting an absent variable with no default
     */
    public function testGettingAbsentVariableWithNoDefault()
    {
        $this->assertNull($this->collection->get('does not exist'));
    }

    /**
     * Tests getting all the parameters
     */
    public function testGettingAll()
    {
        $this->collection->add('foo', 'bar');
        $this->collection->add('bar', 'foo');
        $this->assertEquals([
            'foo' => 'bar',
            'bar' => 'foo'
        ], $this->collection->getAll());
    }

    /**
     * Tests getting as array
     */
    public function testGettingAsArray()
    {
        $this->collection->add('foo', 'bar');
        $this->assertEquals('bar', $this->collection['foo']);
    }

    /**
     * Tests whether the parameters has a certain parameter
     */
    public function testHas()
    {
        $this->assertFalse($this->collection->has('foo'));
        $this->collection->add('foo', 'bar');
        $this->assertTrue($this->collection->has('foo'));
    }

    /**
     * Tests passing parameters through the constructor
     */
    public function testPassingParametersInConstructor()
    {
        $parametersArray = ['foo' => 'bar', 'bar' => 'foo'];
        $parameters = new ErrorCollection($parametersArray);
        $this->assertEquals($parametersArray, $parameters->getAll());
    }

    /**
     * Tests removing a parameter
     */
    public function testRemove()
    {
        $this->collection->add('foo', 'bar');
        $this->collection->remove('foo');
        $this->assertNull($this->collection->get('foo'));
    }

    /**
     * Tests setting a parameter
     */
    public function testSetting()
    {
        $this->collection->set('foo', 'bar');
        $this->assertEquals('bar', $this->collection->get('foo'));
    }

    /**
     * Tests setting an item
     */
    public function testSettingItem()
    {
        $this->collection['foo'] = 'bar';
        $this->assertEquals('bar', $this->collection['foo']);
    }

    /**
     * Tests unsetting a parameter
     */
    public function testUnsetting()
    {
        $this->collection['foo'] = 'bar';
        unset($this->collection['foo']);
        $this->assertNull($this->collection->get('foo'));
    }

    /**
     * Tests unsetting an item
     */
    public function testUnsettingItem()
    {
        $this->collection->add('foo', 'bar');
        unset($this->collection['foo']);
        $this->assertEquals(null, $this->collection['foo']);
    }
}

<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Validation\TestsTemp\Rules\Errors;

use Opulence\Validation\Rules\Errors\ErrorCollection;

/**
 * Tests the error collection
 */
class ErrorCollectionTest extends \PHPUnit\Framework\TestCase
{
    private ErrorCollection $collection;

    protected function setUp(): void
    {
        $this->collection = new ErrorCollection();
    }

    public function testAdding(): void
    {
        $this->collection->add('foo', 'bar');
        $this->assertEquals('bar', $this->collection->get('foo'));
    }

    public function testCheckingOffsetExists(): void
    {
        $this->collection['foo'] = 'bar';
        $this->assertTrue(isset($this->collection['foo']));
    }

    public function testCount(): void
    {
        $this->collection->add('foo', 'bar');
        $this->assertEquals(1, $this->collection->count());
        $this->collection->add('bar', 'foo');
        $this->assertEquals(2, $this->collection->count());
    }

    public function testExchangingArray(): void
    {
        $this->collection->add('foo', 'bar');
        $this->assertEquals(['foo' => 'bar'], $this->collection->exchangeArray(['bar' => 'foo']));
        $this->assertEquals(['bar' => 'foo'], $this->collection->getAll());
    }

    public function testGetting(): void
    {
        $this->collection->add('foo', 'bar');
        $this->assertEquals('bar', $this->collection->get('foo'));
    }

    public function testGettingAbsentVariableWithDefault(): void
    {
        $this->assertEquals('blah', $this->collection->get('does not exist', 'blah'));
    }

    public function testGettingAbsentVariableWithNoDefault(): void
    {
        $this->assertNull($this->collection->get('does not exist'));
    }

    public function testGettingAll(): void
    {
        $this->collection->add('foo', 'bar');
        $this->collection->add('bar', 'foo');
        $this->assertEquals([
            'foo' => 'bar',
            'bar' => 'foo'
        ], $this->collection->getAll());
    }

    public function testGettingAsArray(): void
    {
        $this->collection->add('foo', 'bar');
        $this->assertEquals('bar', $this->collection['foo']);
    }

    public function testHas(): void
    {
        $this->assertFalse($this->collection->has('foo'));
        $this->collection->add('foo', 'bar');
        $this->assertTrue($this->collection->has('foo'));
    }

    public function testPassingParametersInConstructor(): void
    {
        $parametersArray = ['foo' => 'bar', 'bar' => 'foo'];
        $parameters = new ErrorCollection($parametersArray);
        $this->assertEquals($parametersArray, $parameters->getAll());
    }

    public function testRemove(): void
    {
        $this->collection->add('foo', 'bar');
        $this->collection->remove('foo');
        $this->assertNull($this->collection->get('foo'));
    }

    public function testSetting(): void
    {
        $this->collection->set('foo', 'bar');
        $this->assertEquals('bar', $this->collection->get('foo'));
    }

    public function testSettingItem(): void
    {
        $this->collection['foo'] = 'bar';
        $this->assertEquals('bar', $this->collection['foo']);
    }

    public function testUnsetting(): void
    {
        $this->collection['foo'] = 'bar';
        unset($this->collection['foo']);
        $this->assertNull($this->collection->get('foo'));
    }

    public function testUnsettingItem(): void
    {
        $this->collection->add('foo', 'bar');
        unset($this->collection['foo']);
        $this->assertEquals(null, $this->collection['foo']);
    }
}

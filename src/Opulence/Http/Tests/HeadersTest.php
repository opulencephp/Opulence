<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Http\Tests;

use Opulence\Http\Headers;

/**
 * Tests the headers class
 */
class HeadersTest extends \PHPUnit\Framework\TestCase
{
    /** @var Headers The headers to use in tests */
    private $headers = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->headers = new Headers();
    }

    /**
     * Tests that added names are normalized
     */
    public function testAddedNamesAreNormalized()
    {
        $this->headers->add('FOO', 'fooval');
        $this->assertEquals('fooval', $this->headers->get('foo'));
        $this->assertTrue($this->headers->has('foo'));
    }

    /**
     * Tests setting a string value
     */
    public function testAddingStringValue()
    {
        $this->headers->add('foo', 'bar');
        $this->assertEquals('bar', $this->headers->get('foo'));
    }

    /**
     * Tests returning all the values when the key does not exist
     */
    public function testGettingAllValuesWhenKeyDoesNotExist()
    {
        $this->assertEquals('foo', $this->headers->get('THIS_DOES_NOT_EXIST', 'foo', false));
    }

    /**
     * Tests returning only the first value
     */
    public function testGettingFirstValue()
    {
        $this->headers->set('FOO', 'bar');
        $this->assertEquals('bar', $this->headers->get('FOO', null, true));
    }

    /**
     * Tests returning only the first value when the key does not exist
     */
    public function testGettingFirstValueWhenKeyDoesNotExist()
    {
        $this->assertEquals('foo', $this->headers->get('THIS_DOES_NOT_EXIST', 'foo', true));
    }

    /**
     * Tests returning a value
     */
    public function testGettingValue()
    {
        $this->headers->set('FOO', 'bar');
        $this->assertEquals(['bar'], $this->headers->get('FOO', null, false));
    }

    /**
     * Tests that names are case insensitive
     */
    public function testNamesAreCaseInsensitive()
    {
        $headers = new Headers();
        $headers->add('FOO', 'fooval');
        $headers->add('BAR', 'barval');
        $headers->add('BAZ', 'bazval');
        $headers->add('BLAH', 'blahval');
        $this->assertEquals('fooval', $headers->get('foo'));
        $this->assertEquals('barval', $headers->get('bar'));
        $this->assertEquals('bazval', $headers->get('baz'));
        $this->assertEquals('blahval', $headers->get('blah'));
        $this->assertTrue($headers->has('foo'));
        $this->assertTrue($headers->has('bar'));
        $this->assertTrue($headers->has('baz'));
        $this->assertTrue($headers->has('blah'));

        // Remove a name
        $headers->remove('foo');
        $this->assertNull($headers->get('foo'));
        $this->assertFalse($headers->has('foo'));
    }

    /**
     * Tests that set names are normalized
     */
    public function testSetNamesAreNormalized()
    {
        $this->headers->set('FOO', 'fooval');
        $this->assertEquals('fooval', $this->headers->get('foo'));
        $this->assertTrue($this->headers->has('foo'));
    }
}

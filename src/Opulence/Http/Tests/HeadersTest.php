<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Http\Tests;

use Opulence\Http\Headers;

/**
 * Tests the headers class
 */
class HeadersTest extends \PHPUnit\Framework\TestCase
{
    /** @var Headers The headers to use in tests */
    private $headers;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->headers = new Headers();
    }

    /**
     * Tests that added names are normalized
     */
    public function testAddedNamesAreNormalized(): void
    {
        $this->headers->add('FOO', 'fooval');
        $this->assertEquals('fooval', $this->headers->get('foo'));
        $this->assertTrue($this->headers->has('foo'));
    }

    /**
     * Tests setting a string value
     */
    public function testAddingStringValue(): void
    {
        $this->headers->add('foo', 'bar');
        $this->assertEquals('bar', $this->headers->get('foo'));
    }

    /**
     * Tests returning all the values when the key does not exist
     */
    public function testGettingAllValuesWhenKeyDoesNotExist(): void
    {
        $this->assertEquals('foo', $this->headers->get('THIS_DOES_NOT_EXIST', 'foo', false));
    }

    /**
     * Tests returning only the first value
     */
    public function testGettingFirstValue(): void
    {
        $this->headers->set('FOO', 'bar');
        $this->assertEquals('bar', $this->headers->get('FOO', null, true));
    }

    /**
     * Tests returning only the first value when the key does not exist
     */
    public function testGettingFirstValueWhenKeyDoesNotExist(): void
    {
        $this->assertEquals('foo', $this->headers->get('THIS_DOES_NOT_EXIST', 'foo', true));
    }

    /**
     * Tests returning a value
     */
    public function testGettingValue(): void
    {
        $this->headers->set('FOO', 'bar');
        $this->assertEquals(['bar'], $this->headers->get('FOO', null, false));
    }

    /**
     * Tests that names are case insensitive
     */
    public function testNamesAreCaseInsensitive(): void
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
    public function testSetNamesAreNormalized(): void
    {
        $this->headers->set('FOO', 'fooval');
        $this->assertEquals('fooval', $this->headers->get('foo'));
        $this->assertTrue($this->headers->has('foo'));
    }
}

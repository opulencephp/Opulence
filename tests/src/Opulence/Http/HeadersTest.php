<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Http;

/**
 * Tests the headers class
 */
class HeadersTest extends \PHPUnit\Framework\TestCase
{
    /** @var Headers The headers to use in tests */
    private $headers = null;
    /** @var array The server array to use */
    private $serverArray = [
        'NON_HEADER' => 'foo',
        'CONTENT_LENGTH' => 4,
        'CONTENT_TYPE' => 'foo',
        'HTTP_ACCEPT' => 'accept',
        'HTTP_ACCEPT_CHARSET' => 'accept_charset',
        'HTTP_ACCEPT_ENCODING' => 'accept_encoding',
        'HTTP_ACCEPT_LANGUAGE' => 'accept_language',
        'HTTP_CONNECTION' => 'connection',
        'HTTP_HOST' => 'host',
        'HTTP_REFERER' => 'referer',
        'HTTP_USER_AGENT' => 'user_agent'
    ];

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->headers = new Headers($this->serverArray);
    }

    /**
     * Tests that added names are normalized
     */
    public function testAddedNamesAreNormalized()
    {
        $this->headers->add('HTTP_FOO', 'fooval');
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
     * Tests getting all the headers after setting them in the constructor
     */
    public function testGettingAllAfterSettingInConstructor()
    {
        $headerParameters = [];

        foreach ($this->serverArray as $key => $value) {
            $key = strtoupper($key);

            if (strpos($key, 'HTTP_') === 0) {
                if (!is_array($value)) {
                    $value = [$value];
                }

                $headerParameters[$this->normalizeName($key)] = $value;
            } elseif (strpos($key, 'CONTENT_') === 0) {
                if (!is_array($value)) {
                    $value = [$value];
                }

                $headerParameters[$this->normalizeName($key)] = $value;
            }
        }

        $this->assertEquals($headerParameters, $this->headers->getAll());
    }

    /**
     * Tests returning all the values
     */
    public function testGettingAllValues()
    {
        $this->assertEquals(['host'], $this->headers->get('HOST', null, false));
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
        $this->assertEquals('host', $this->headers->get('HOST', null, true));
    }

    /**
     * Tests returning only the first value when the key does not exist
     */
    public function testGettingFirstValueWhenKeyDoesNotExist()
    {
        $this->assertEquals('foo', $this->headers->get('THIS_DOES_NOT_EXIST', 'foo', true));
    }

    /**
     * Tests that names are case insensitive
     */
    public function testNamesAreCaseInsensitive()
    {
        $headers = new Headers(['HTTP_FOO' => 'fooval', 'CONTENT_LENGTH' => 'lengthval']);
        $headers->add('HTTP_BAZ', 'bazval');
        $headers->add('HTTP_BLAH', 'blahval');
        $this->assertEquals('fooval', $headers->get('foo'));
        $this->assertEquals('lengthval', $headers->get('content_length'));
        $this->assertEquals('bazval', $headers->get('http_baz'));
        $this->assertEquals('blahval', $headers->get('http_blah'));
        $this->assertTrue($headers->has('foo'));
        $this->assertTrue($headers->has('content_length'));
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
        $this->headers->set('HTTP_FOO', 'fooval');
        $this->assertEquals('fooval', $this->headers->get('foo'));
        $this->assertTrue($this->headers->has('foo'));
    }

    /**
     * Normalizes a name
     *
     * @param string $name The name to normalize
     * @return string The normalized name
     */
    private function normalizeName($name)
    {
        $name = strtr(strtolower($name), '_', '-');

        if (strpos($name, 'http-') === 0) {
            $name = substr($name, 5);
        }

        return $name;
    }
}

<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Http\Tests\Requests;

use Opulence\Http\Requests\RequestHeaders;

/**
 * Tests the request headers
 */
class RequestHeadersTest extends \PHPUnit\Framework\TestCase
{
    /** @var RequestHeaders The headers to use in tests */
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
        $this->headers = new RequestHeaders($this->serverArray);
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

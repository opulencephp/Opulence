<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Tests\Tokens\JsonWebTokens;

use InvalidArgumentException;
use Opulence\Authentication\Tokens\JsonWebTokens\JwtHeader;
use Opulence\Authentication\Tokens\Signatures\Algorithms;

/**
 * Tests the JWT header
 */
class JwtHeaderTest extends \PHPUnit\Framework\TestCase
{
    /** @var JwtHeader The header to use in tests */
    private $header;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->header = new JwtHeader(Algorithms::SHA512);
    }

    /**
     * Tests that the default algorithm is SHA256
     */
    public function testDefaultAlgorithmIsSha256(): void
    {
        $header = new JwtHeader();
        $this->assertEquals('HS256', $header->getAlgorithm());
    }

    /**
     * Tests getting the algorithm
     */
    public function testGettingAlgorithm(): void
    {
        $this->assertEquals('HS512', $this->header->getAlgorithm());
    }

    /**
     * Tests getting all values
     */
    public function testGettingAllValues(): void
    {
        $expected = [
            'typ' => 'JWT',
            'alg' => 'HS512'
        ];
        $this->assertEquals($expected, $this->header->getAll());
        $this->header->add('foo', 'bar');
        $expected['foo'] = 'bar';
        $this->assertEquals($expected, $this->header->getAll());
    }

    /**
     * Tests getting the content type
     */
    public function testGettingContentType(): void
    {
        $this->header->add('cty', 'JWT');
        $this->assertEquals('JWT', $this->header->getContentType());
    }

    /**
     * Tests getting the encoded string
     */
    public function testGettingEncodedString(): void
    {
        $headers = [
            'typ' => 'JWT',
            'alg' => 'HS512'
        ];
        $this->assertEquals(
            rtrim(strtr(base64_encode(json_encode($headers)), '+/', '-_'), '='),
            $this->header->encode()
        );
        $this->header->add('foo', 'bar');
        $headers['foo'] = 'bar';
        $this->assertEquals(
            rtrim(strtr(base64_encode(json_encode($headers)), '+/', '-_'), '='),
            $this->header->encode()
        );
    }

    /**
     * Tests getting the token type
     */
    public function testGettingTokenType(): void
    {
        $this->assertEquals('JWT', $this->header->getTokenType());
    }

    /**
     * Tests getting the value for an extra header
     */
    public function testGettingValue(): void
    {
        $this->assertNull($this->header->get('foo'));
        $this->header->add('foo', 'bar');
        $this->assertEquals('bar', $this->header->get('foo'));
        $this->header->add('foo', 'baz');
        $this->assertEquals('baz', $this->header->get('foo'));
    }

    /**
     * Tests that an invalid algorithm in the constructor throws an exception
     */
    public function testInvalidAlgorithmInConstructorThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new JwtHeader('foo');
    }

    /**
     * Tests setting the "none" algorithm
     */
    public function testSettingNoneAlgorithm(): void
    {
        $this->header->add('alg', 'none');
        $this->assertEquals('none', $this->header->getAlgorithm());
    }
}

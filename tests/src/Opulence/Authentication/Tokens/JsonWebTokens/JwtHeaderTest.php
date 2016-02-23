<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens\JsonWebTokens;

use InvalidArgumentException;
use Opulence\Authentication\Tokens\JsonWebTokens\Signature\JwsAlgorithms;

/**
 * Tests the JWT header
 */
class JwtHeaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var JwtHeader The header to use in tests */
    private $header = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->header = new JwtHeader(JwsAlgorithms::SHA512);
    }

    /**
     * Tests that the default algorithm is SHA256
     */
    public function testDefaultAlgorithmIsSha256()
    {
        $header = new JwtHeader();
        $this->assertEquals("HS256", $header->getAlgorithm());
    }

    /**
     * Tests getting the algorithm
     */
    public function testGettingAlgorithm()
    {
        $this->assertEquals("HS512", $this->header->getAlgorithm());
    }

    /**
     * Tests getting all values
     */
    public function testGettingAllValues()
    {
        $expected = [
            "typ" => "JWT",
            "cty" => "JWT",
            "alg" => "HS512"
        ];
        $this->assertEquals($expected, $this->header->getAll());
        $this->header->add("foo", "bar");
        $expected["foo"] = "bar";
        $this->assertEquals($expected, $this->header->getAll());
    }

    /**
     * Tests getting the content type
     */
    public function testGettingContentType()
    {
        $this->assertEquals("JWT", $this->header->getContentType());
    }

    /**
     * Tests getting the encoded string
     */
    public function testGettingEncodedString()
    {
        $headers = [
            "typ" => "JWT",
            "cty" => "JWT",
            "alg" => "HS512"
        ];
        $this->assertEquals(
            rtrim(strtr(base64_encode(json_encode($headers)), "+/", "-_"), "="),
            $this->header->encode()
        );
        $this->header->add("foo", "bar");
        $headers["foo"] = "bar";
        $this->assertEquals(
            rtrim(strtr(base64_encode(json_encode($headers)), "+/", "-_"), "="),
            $this->header->encode()
        );
    }

    /**
     * Tests getting the token type
     */
    public function testGettingTokenType()
    {
        $this->assertEquals("JWT", $this->header->getTokenType());
    }

    /**
     * Tests getting the value for an extra header
     */
    public function testGettingValue()
    {
        $this->assertNull($this->header->get("foo"));
        $this->header->add("foo", "bar");
        $this->assertEquals("bar", $this->header->get("foo"));
        $this->header->add("foo", "baz");
        $this->assertEquals("baz", $this->header->get("foo"));
    }

    /**
     * Tests that an invalid algorithm in the constructor throws an exception
     */
    public function testInvalidAlgorithmInConstructorThrowsException()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        new JwtHeader("foo");
    }
}
<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens\JsonWebTokens;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Tests the JSON web token
 */
class JwtTest extends \PHPUnit_Framework_TestCase
{
    /** @var Jwt The JWT to use in tests */
    private $jwt = null;
    /** @var JwtHeader The header to use in tests */
    private $header = null;
    /** @var JwtPayload The payload to use in tests */
    private $payload = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->header = new JwtHeader();
        $this->payload = new JwtPayload();
        $this->jwt = new Jwt($this->header, $this->payload);
    }

    /**
     * Tests creating token from string
     */
    public function testDecodingEncodedToken()
    {
        $token = $this->jwt->encode("foo");
        $this->assertEquals($this->jwt, Jwt::createFromString($token, "foo"));
    }

    /**
     * Tests encoding and decoding RSA algorithms
     */
    public function testEncodingDecodingRsaAlgorithms()
    {
        $algorithms = [
            JwtAlgorithms::RSA_SHA256 => "sha256",
            JwtAlgorithms::RSA_SHA384 => "sha384",
            JwtAlgorithms::RSA_SHA512 => "sha512"
        ];

        foreach ($algorithms as $algorithm => $digestAlgorithm) {
            $privateKey = openssl_pkey_new(
                [
                    "digest_alg" => $digestAlgorithm,
                    "private_key_bits" => 1024,
                    "private_key_type" => OPENSSL_KEYTYPE_RSA
                ]
            );
            $publicKey = openssl_pkey_get_details($privateKey);
            $this->jwt->getHeader()->add("alg", $algorithm);
            $token = $this->jwt->encode($privateKey);
            $this->assertEquals($this->jwt, Jwt::createFromString($token, $publicKey["key"], true));
        }
    }

    /**
     * Tests verifying an expired token throws an exception
     */
    public function testExceptionThrownWhenVerifyingExpiredToken()
    {
        $this->setExpectedException(SignatureVerificationException::class);
        $this->jwt->getPayload()->setValidTo(new DateTimeImmutable("-30 second"));
        $token = $this->jwt->encode("foo");
        Jwt::createFromString($token, "foo", true);
    }

    /**
     * Tests that an exception is thrown with an empty key
     */
    public function testExceptionThrownWithEmptyKey()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $token = $this->jwt->encode("foo");
        Jwt::createFromString($token, "");
    }

    /**
     * Tests that an exception is thrown with an invalid number of segments
     */
    public function testExceptionThrownWithInvalidNumberSegments()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        Jwt::createFromString("foo.bar", "baz");
    }

    /**
     * Tests verifying an NBF in the future throws an exception
     */
    public function testExceptionThrownWithNbfInFuture()
    {
        $this->setExpectedException(SignatureVerificationException::class);
        $this->jwt->getPayload()->setValidFrom(new DateTimeImmutable("+30 second"));
        $token = $this->jwt->encode("foo");
        Jwt::createFromString($token, "foo", true);
    }

    /**
     * Tests that an exception is thrown with no algorithm set
     */
    public function testExceptionThrownWithNoAlgorithmSet()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        Jwt::createFromString(base64_encode("foo") . "." . base64_encode("bar") . "." . base64_encode("baz"), "blah");
    }

    /**
     * Tests getting the header
     */
    public function testGettingHeader()
    {
        $this->assertSame($this->header, $this->jwt->getHeader());
    }

    /**
     * Tests getting the payload
     */
    public function testGettingPayload()
    {
        $this->assertSame($this->payload, $this->jwt->getPayload());
    }

    /**
     * Tests that an invalid key throws an exception
     */
    public function testInvalidKeyThrowsException()
    {
        $this->setExpectedException(SignatureVerificationException::class);
        $token = $this->jwt->encode("foo");
        Jwt::createFromString($token, "bar", true);
    }

    /**
     * Tests that an invalid signature throws an exception
     */
    public function testInvalidSignatureThrowsException()
    {
        $this->setExpectedException(SignatureVerificationException::class);
        $token = base64_encode(json_encode(["alg" => "HS256"])) . "." . base64_encode(json_encode("foo")) . ".bar";
        Jwt::createFromString($token, "baz");
    }

    /**
     * Tests verifying NBF
     */
    public function testVerifyingNbf()
    {
        $this->jwt->getPayload()->setValidFrom(new DateTimeImmutable("-30 second"));
        $token = $this->jwt->encode("foo");
        $this->assertEquals($this->jwt, Jwt::createFromString($token, "foo", true));
    }

    /**
     * Tests verifying an unexpired token
     */
    public function testVerifyingUnexpiredToken()
    {
        $this->jwt->getPayload()->setValidTo(new DateTimeImmutable("+30 second"));
        $token = $this->jwt->encode("foo");
        $this->assertEquals($this->jwt, Jwt::createFromString($token, "foo", true));
    }
}
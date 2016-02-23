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
use LogicException;
use Opulence\Authentication\Tokens\JsonWebTokens\Signature\Jws;
use Opulence\Authentication\Tokens\JsonWebTokens\Signature\JwsAlgorithms;

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
        $jws = new Jws(JwsAlgorithms::SHA256, "public");
        $jws->sign($this->jwt);
        $token = $this->jwt->encode();
        $this->assertTokensEqual($this->jwt, Jwt::createFromString($token), false);
    }

    /**
     * Tests encoding and decoding RSA algorithms
     */
    public function testEncodingDecodingRsaAlgorithms()
    {
        $algorithms = [
            JwsAlgorithms::RSA_SHA256 => "sha256",
            JwsAlgorithms::RSA_SHA384 => "sha384",
            JwsAlgorithms::RSA_SHA512 => "sha512"
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
            $jws = new Jws($algorithm, $publicKey["key"], $privateKey);
            $jws->sign($this->jwt);
            $token = $this->jwt->encode();
            $this->assertTokensEqual($this->jwt, Jwt::createFromString($token), false);
        }
    }

    /**
     * Tests an exception is thrown when encoding a token without a signature
     */
    public function testExceptionThrownWhenEncodingTokenWithoutSignature()
    {
        $this->setExpectedException(LogicException::class);
        $this->jwt->encode();
    }

    /**
     * Tests that an exception is thrown with an invalid number of segments
     */
    public function testExceptionThrownWithInvalidNumberSegments()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        Jwt::createFromString("foo.bar");
    }

    /**
     * Tests that an exception is thrown with no algorithm set
     */
    public function testExceptionThrownWithNoAlgorithmSet()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        Jwt::createFromString(base64_encode("foo") . "." . base64_encode("bar") . "." . base64_encode("baz"));
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
     * Tests getting the signature
     */
    public function testGettingSignature()
    {
        $jwtWithSignature = new Jwt($this->header, $this->payload, "signature");
        $this->assertEquals("signature", $jwtWithSignature->getSignature());
    }

    /**
     * Tests setting the signature
     */
    public function testSettingSignature()
    {
        $this->jwt->setSignature("new-signature");
        $this->assertEquals("new-signature", $this->jwt->getSignature());
    }

    private function assertTokensEqual(Jwt $a, Jwt $b, bool $checkSignature)
    {
        $this->assertEquals($a->getHeader(), $b->getHeader());
        $this->assertEquals($a->getPayload(), $b->getPayload());

        if ($checkSignature) {
            $this->assertEquals($a->getSignature(), $b->getSignature());
        }
    }
}
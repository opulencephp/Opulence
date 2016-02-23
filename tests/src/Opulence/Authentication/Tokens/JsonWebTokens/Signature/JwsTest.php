<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens\JsonWebTokens\Signature;

use Opulence\Authentication\Tokens\JsonWebTokens\Jwt;
use Opulence\Authentication\Tokens\JsonWebTokens\JwtHeader;
use Opulence\Authentication\Tokens\JsonWebTokens\JwtPayload;

/**
 * Tests the JWS
 */
class JwsTest extends \PHPUnit_Framework_TestCase
{
    /** @var Jwt The token to use in tests */
    private $jwt = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->jwt = new Jwt(new JwtHeader(), new JwtPayload());
    }

    /**
     * Tests getting the algorithm
     */
    public function testGettingAlgorithm()
    {
        $jws = new Jws(JwsAlgorithms::RSA_SHA512, "public", "private");
        $this->assertEquals(JwsAlgorithms::RSA_SHA512, $jws->getAlgorithm());
    }

    /**
     * Tests getting the unsigned value
     */
    public function testGettingUnsignedValue()
    {
        $jws = new Jws(JwsAlgorithms::SHA256, "public", "private");
        $this->assertEquals(
            "{$this->jwt->getHeader()->encode()}.{$this->jwt->getPayload()->encode()}",
            $jws->getUnsignedValue($this->jwt)
        );
    }

    /**
     * Tests signing with asymmetric algorithms
     */
    public function testSigningWithAsymmetricAlgorithms()
    {
        $algorithms = [
            JwsAlgorithms::RSA_SHA256 => [OPENSSL_ALGO_SHA256, "sha256"],
            JwsAlgorithms::RSA_SHA384 => [OPENSSL_ALGO_SHA384, "sha384"],
            JwsAlgorithms::RSA_SHA512 => [OPENSSL_ALGO_SHA512, "sha512"]
        ];

        foreach ($algorithms as $jwtAlgorithm => $algorithmData) {
            $this->jwt->getHeader()->add("alg", $jwtAlgorithm);
            $privateKey = openssl_pkey_new(
                [
                    "digest_alg" => $algorithmData[1],
                    "private_key_bits" => 1024,
                    "private_key_type" => OPENSSL_KEYTYPE_RSA
                ]
            );
            $publicKey = openssl_pkey_get_details($privateKey);
            $jws = new Jws($jwtAlgorithm, $publicKey, $privateKey);
            $jws->sign($this->jwt);
            $signature = "";
            openssl_sign($jws->getUnsignedValue($this->jwt), $signature, $privateKey, $algorithmData[0]);
            $this->assertEquals($signature, $this->jwt->getSignature());
        }
    }

    /**
     * Tests signing with symmetric algorithms
     */
    public function testSigningWithSymmetricAlgorithms()
    {
        $algorithms = [
            JwsAlgorithms::SHA256 => "sha256",
            JwsAlgorithms::SHA384 => "sha384",
            JwsAlgorithms::SHA512 => "sha512"
        ];

        foreach ($algorithms as $jwtAlgorithm => $hashAlgorithm) {
            $this->jwt->getHeader()->add("alg", $jwtAlgorithm);
            $jws = new Jws($jwtAlgorithm, "public");
            $jws->sign($this->jwt);
            $this->assertEquals(
                hash_hmac($hashAlgorithm, $jws->getUnsignedValue($this->jwt), "public", true),
                $this->jwt->getSignature()
            );
        }
    }

    /**
     * Tests verifying asymmetric algorithms
     */
    public function testVerifyingAsymmetricAlgorithms()
    {
        $algorithms = [
            JwsAlgorithms::RSA_SHA256 => [OPENSSL_ALGO_SHA256, "sha256"],
            JwsAlgorithms::RSA_SHA384 => [OPENSSL_ALGO_SHA384, "sha384"],
            JwsAlgorithms::RSA_SHA512 => [OPENSSL_ALGO_SHA512, "sha512"]
        ];
        $numVerified = 0;
        $numUnverified = 0;

        foreach ($algorithms as $jwtAlgorithm => $algorithmData) {
            $this->jwt->getHeader()->add("alg", $jwtAlgorithm);
            $privateKey = openssl_pkey_new(
                [
                    "digest_alg" => $algorithmData[1],
                    "private_key_bits" => 1024,
                    "private_key_type" => OPENSSL_KEYTYPE_RSA
                ]
            );
            $publicKey = openssl_pkey_get_details($privateKey);
            $jws = new Jws($jwtAlgorithm, $publicKey["key"], $privateKey);
            openssl_sign($jws->getUnsignedValue($this->jwt), $correctSignature, $privateKey, $algorithmData[0]);
            $signatures = [$correctSignature, "incorrect"];

            foreach ($signatures as $signature) {
                $this->jwt->setSignature($signature);
                if ($jws->verify($this->jwt)) {
                    $numVerified++;
                } else {
                    $numUnverified++;
                }
            }
        }

        $this->assertEquals(count($algorithms), $numVerified);
        $this->assertEquals(count($algorithms), $numUnverified);
    }

    /**
     * Tests that verifying an empty signature returns false
     */
    public function testVerifyingEmptySignatureReturnsFalse()
    {
        $jws = new Jws(JwsAlgorithms::SHA256, "public");
        $this->assertFalse($jws->verify($this->jwt));
    }

    /**
     * Tests verifying symmetric algorithms
     */
    public function testVerifyingSymmetricAlgorithms()
    {
        $algorithms = [
            JwsAlgorithms::SHA256 => "sha256",
            JwsAlgorithms::SHA384 => "sha384",
            JwsAlgorithms::SHA512 => "sha512"
        ];
        $numVerified = 0;
        $numUnverified = 0;

        foreach ($algorithms as $jwtAlgorithm => $hashAlgorithm) {
            $this->jwt->getHeader()->add("alg", $jwtAlgorithm);
            $jws = new Jws($jwtAlgorithm, "public");
            $signatures = [
                hash_hmac($hashAlgorithm, $jws->getUnsignedValue($this->jwt), "public", true),
                hash_hmac($hashAlgorithm, $jws->getUnsignedValue($this->jwt), "incorrect", true),
            ];

            foreach ($signatures as $signature) {
                $this->jwt->setSignature($signature);
                if ($jws->verify($this->jwt)) {
                    $numVerified++;
                } else {
                    $numUnverified++;
                }
            }
        }

        $this->assertEquals(count($algorithms), $numVerified);
        $this->assertEquals(count($algorithms), $numUnverified);
    }
}
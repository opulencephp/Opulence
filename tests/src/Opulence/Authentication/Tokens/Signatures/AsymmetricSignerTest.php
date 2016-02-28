<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens\Signatures;

use Opulence\Authentication\Tokens\ISignedToken;
use Opulence\Authentication\Tokens\IUnsignedToken;

/**
 * Tests the asymmetric signer
 */
class AsymmetricSignerTest extends \PHPUnit_Framework_TestCase
{
    /** @var IUnsignedToken|\PHPUnit_Framework_MockObject_MockObject The unsigned token to use in tests */
    private $unsignedToken = null;
    /** @var ISignedToken|\PHPUnit_Framework_MockObject_MockObject The signed token to use in tests */
    private $signedToken = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->unsignedToken = $this->getMock(IUnsignedToken::class);
        $this->unsignedToken->expects($this->any())
            ->method("getUnsignedValue")
            ->willReturn("unsignedValue");
        $this->signedToken = $this->getMock(ISignedToken::class);
        $this->signedToken->expects($this->any())
            ->method("getUnsignedValue")
            ->willReturn("unsignedValue");
    }

    /**
     * Tests getting the algorithm
     */
    public function testGettingAlgorithm()
    {
        $signer = new AsymmetricSigner(Algorithms::RSA_SHA512, "public", "private");
        $this->assertEquals(Algorithms::RSA_SHA512, $signer->getAlgorithm());
    }

    /**
     * Tests signing with asymmetric algorithms
     */
    public function testSigningWithAsymmetricAlgorithms()
    {
        $algorithms = [
            Algorithms::RSA_SHA256 => [OPENSSL_ALGO_SHA256, "sha256"],
            Algorithms::RSA_SHA384 => [OPENSSL_ALGO_SHA384, "sha384"],
            Algorithms::RSA_SHA512 => [OPENSSL_ALGO_SHA512, "sha512"]
        ];

        foreach ($algorithms as $algorithm => $algorithmData) {
            $privateKey = openssl_pkey_new(
                [
                    "digest_alg" => $algorithmData[1],
                    "private_key_bits" => 1024,
                    "private_key_type" => OPENSSL_KEYTYPE_RSA
                ]
            );
            $publicKey = openssl_pkey_get_details($privateKey);
            $signer = new AsymmetricSigner($algorithm, $publicKey, $privateKey);
            $signature = "";
            openssl_sign($this->unsignedToken->getUnsignedValue(), $signature, $privateKey, $algorithmData[0]);
            $this->assertEquals($signature, $signer->sign($this->unsignedToken->getUnsignedValue()));
        }
    }

    /**
     * Tests verifying asymmetric algorithms
     */
    public function testVerifyingAsymmetricAlgorithms()
    {
        $algorithms = [
            Algorithms::RSA_SHA256 => [OPENSSL_ALGO_SHA256, "sha256"],
            Algorithms::RSA_SHA384 => [OPENSSL_ALGO_SHA384, "sha384"],
            Algorithms::RSA_SHA512 => [OPENSSL_ALGO_SHA512, "sha512"]
        ];
        $numVerified = 0;
        $numUnverified = 0;

        foreach ($algorithms as $jwtAlgorithm => $algorithmData) {
            $privateKey = openssl_pkey_new(
                [
                    "digest_alg" => $algorithmData[1],
                    "private_key_bits" => 1024,
                    "private_key_type" => OPENSSL_KEYTYPE_RSA
                ]
            );
            $publicKey = openssl_pkey_get_details($privateKey);
            $signer = new AsymmetricSigner($jwtAlgorithm, $publicKey["key"], $privateKey);
            openssl_sign($this->unsignedToken->getUnsignedValue(), $correctSignature, $privateKey, $algorithmData[0]);
            $signatures = [$correctSignature, "incorrect"];

            foreach ($signatures as $signature) {
                if ($signer->verify($this->signedToken->getUnsignedValue(), $signature)) {
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
        $jws = new AsymmetricSigner(Algorithms::SHA256, "public", "private");
        $this->assertFalse($jws->verify($this->signedToken->getUnsignedValue(), ""));
    }
}
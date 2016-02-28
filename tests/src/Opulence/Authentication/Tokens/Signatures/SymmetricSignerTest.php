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
 * Tests the symmetric signer
 */
class SymmetricSignerTest extends \PHPUnit_Framework_TestCase
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
        $signer = new SymmetricSigner(Algorithms::RSA_SHA512, "public", "private");
        $this->assertEquals(Algorithms::RSA_SHA512, $signer->getAlgorithm());
    }

    /**
     * Tests signing with symmetric algorithms
     */
    public function testSigningWithSymmetricAlgorithms()
    {
        $algorithms = [
            Algorithms::SHA256 => "sha256",
            Algorithms::SHA384 => "sha384",
            Algorithms::SHA512 => "sha512"
        ];

        foreach ($algorithms as $jwtAlgorithm => $hashAlgorithm) {
            $signer = new SymmetricSigner($jwtAlgorithm, "public");
            $this->assertEquals(
                hash_hmac($hashAlgorithm, $this->unsignedToken->getUnsignedValue(), "public", true),
                $signer->sign($this->unsignedToken->getUnsignedValue())
            );
        }
    }

    /**
     * Tests that verifying an empty signature returns false
     */
    public function testVerifyingEmptySignatureReturnsFalse()
    {
        $jws = new SymmetricSigner(Algorithms::SHA256, "public");
        $this->assertFalse($jws->verify($this->signedToken->getUnsignedValue(), ""));
    }

    /**
     * Tests verifying symmetric algorithms
     */
    public function testVerifyingSymmetricAlgorithms()
    {
        $algorithms = [
            Algorithms::SHA256 => "sha256",
            Algorithms::SHA384 => "sha384",
            Algorithms::SHA512 => "sha512"
        ];
        $numVerified = 0;
        $numUnverified = 0;

        foreach ($algorithms as $jwtAlgorithm => $hashAlgorithm) {
            $signer = new SymmetricSigner($jwtAlgorithm, "public");
            $signatures = [
                hash_hmac($hashAlgorithm, $this->unsignedToken->getUnsignedValue(), "public", true),
                hash_hmac($hashAlgorithm, $this->unsignedToken->getUnsignedValue(), "incorrect", true),
            ];

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
}
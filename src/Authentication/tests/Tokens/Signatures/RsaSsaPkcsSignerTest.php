<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Tests\Tokens\Signatures;

use Opulence\Authentication\Tokens\ISignedToken;
use Opulence\Authentication\Tokens\IUnsignedToken;
use Opulence\Authentication\Tokens\Signatures\Algorithms;
use Opulence\Authentication\Tokens\Signatures\RsaSsaPkcsSigner;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests the RSA SSA PKCS signer
 */
class RsaSsaPkcsSignerTest extends TestCase
{
    /** @var IUnsignedToken|MockObject The unsigned token to use in tests */
    private IUnsignedToken $unsignedToken;
    /** @var ISignedToken|MockObject The signed token to use in tests */
    private ISignedToken $signedToken;

    protected function setUp(): void
    {
        $this->unsignedToken = $this->createMock(IUnsignedToken::class);
        $this->unsignedToken->expects($this->any())
            ->method('getUnsignedValue')
            ->willReturn('unsignedValue');
        $this->signedToken = $this->createMock(ISignedToken::class);
        $this->signedToken->expects($this->any())
            ->method('getUnsignedValue')
            ->willReturn('unsignedValue');
    }

    public function testGettingAlgorithm(): void
    {
        $signer = new RsaSsaPkcsSigner(Algorithms::RSA_SHA512, 'public', 'private');
        $this->assertEquals(Algorithms::RSA_SHA512, $signer->getAlgorithm());
    }

    public function testSigningWithAsymmetricAlgorithms(): void
    {
        $algorithms = [
            Algorithms::RSA_SHA256 => [OPENSSL_ALGO_SHA256, 'sha256'],
            Algorithms::RSA_SHA384 => [OPENSSL_ALGO_SHA384, 'sha384'],
            Algorithms::RSA_SHA512 => [OPENSSL_ALGO_SHA512, 'sha512']
        ];

        foreach ($algorithms as $algorithm => $algorithmData) {
            $privateKey = openssl_pkey_new(
                [
                    'digest_alg' => $algorithmData[1],
                    'private_key_bits' => 1024,
                    'private_key_type' => OPENSSL_KEYTYPE_RSA
                ]
            );
            $publicKey = openssl_pkey_get_details($privateKey);
            $signer = new RsaSsaPkcsSigner($algorithm, $publicKey, $privateKey);
            $signature = '';
            openssl_sign($this->unsignedToken->getUnsignedValue(), $signature, $privateKey, $algorithmData[0]);
            $this->assertEquals($signature, $signer->sign($this->unsignedToken->getUnsignedValue()));
        }
    }

    public function testVerifyingAsymmetricAlgorithms(): void
    {
        $algorithms = [
            Algorithms::RSA_SHA256 => [OPENSSL_ALGO_SHA256, 'sha256'],
            Algorithms::RSA_SHA384 => [OPENSSL_ALGO_SHA384, 'sha384'],
            Algorithms::RSA_SHA512 => [OPENSSL_ALGO_SHA512, 'sha512']
        ];
        $numVerified = 0;
        $numUnverified = 0;

        foreach ($algorithms as $jwtAlgorithm => $algorithmData) {
            $privateKey = openssl_pkey_new(
                [
                    'digest_alg' => $algorithmData[1],
                    'private_key_bits' => 1024,
                    'private_key_type' => OPENSSL_KEYTYPE_RSA
                ]
            );
            $publicKey = openssl_pkey_get_details($privateKey);
            $signer = new RsaSsaPkcsSigner($jwtAlgorithm, $publicKey['key'], $privateKey);
            openssl_sign($this->unsignedToken->getUnsignedValue(), $correctSignature, $privateKey, $algorithmData[0]);
            $signatures = [$correctSignature, 'incorrect'];

            foreach ($signatures as $signature) {
                if ($signer->verify($this->signedToken->getUnsignedValue(), $signature)) {
                    $numVerified++;
                } else {
                    $numUnverified++;
                }
            }
        }

        $this->assertCount($numVerified, $algorithms);
        $this->assertCount($numUnverified, $algorithms);
    }

    public function testVerifyingEmptySignatureReturnsFalse(): void
    {
        $jws = new RsaSsaPkcsSigner(Algorithms::SHA256, 'public', 'private');
        $this->assertFalse($jws->verify($this->signedToken->getUnsignedValue(), ''));
    }
}

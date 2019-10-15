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
use Opulence\Authentication\Tokens\Signatures\HmacSigner;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests the HMAC signer
 */
class HmacSignerTest extends TestCase
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
        $signer = new HmacSigner(Algorithms::RSA_SHA512, 'public', 'private');
        $this->assertEquals(Algorithms::RSA_SHA512, $signer->getAlgorithm());
    }

    public function testSigningWithSymmetricAlgorithms(): void
    {
        $algorithms = [
            Algorithms::SHA256 => 'sha256',
            Algorithms::SHA384 => 'sha384',
            Algorithms::SHA512 => 'sha512'
        ];

        foreach ($algorithms as $jwtAlgorithm => $hashAlgorithm) {
            $signer = new HmacSigner($jwtAlgorithm, 'public');
            $this->assertEquals(
                hash_hmac($hashAlgorithm, $this->unsignedToken->getUnsignedValue(), 'public', true),
                $signer->sign($this->unsignedToken->getUnsignedValue())
            );
        }
    }

    public function testVerifyingEmptySignatureReturnsFalse(): void
    {
        $jws = new HmacSigner(Algorithms::SHA256, 'public');
        $this->assertFalse($jws->verify($this->signedToken->getUnsignedValue(), ''));
    }

    public function testVerifyingSymmetricAlgorithms(): void
    {
        $algorithms = [
            Algorithms::SHA256 => 'sha256',
            Algorithms::SHA384 => 'sha384',
            Algorithms::SHA512 => 'sha512'
        ];
        $numVerified = 0;
        $numUnverified = 0;

        foreach ($algorithms as $jwtAlgorithm => $hashAlgorithm) {
            $signer = new HmacSigner($jwtAlgorithm, 'public');
            $signatures = [
                hash_hmac($hashAlgorithm, $this->unsignedToken->getUnsignedValue(), 'public', true),
                hash_hmac($hashAlgorithm, $this->unsignedToken->getUnsignedValue(), 'incorrect', true),
            ];

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
}

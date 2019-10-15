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
use Opulence\Authentication\Tokens\JsonWebTokens\JwtPayload;
use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;
use Opulence\Authentication\Tokens\JsonWebTokens\UnsignedJwt;
use Opulence\Authentication\Tokens\Signatures\Algorithms;
use Opulence\Authentication\Tokens\Signatures\HmacSigner;
use Opulence\Authentication\Tokens\Signatures\RsaSsaPkcsSigner;
use PHPUnit\Framework\TestCase;

/**
 * Tests the signed JWT
 */
class SignedJwtTest extends TestCase
{
    private JwtHeader $header;
    private JwtPayload $payload;

    protected function setUp(): void
    {
        $this->header = new JwtHeader();
        $this->payload = new JwtPayload();
    }

    public function testCreatingFromUnsignedToken(): void
    {
        $signedJwt = SignedJwt::createFromUnsignedJwt(new UnsignedJwt($this->header, $this->payload), 'foo');
        $this->assertSame($this->header, $signedJwt->getHeader());
        $this->assertSame($this->payload, $signedJwt->getPayload());
        $this->assertEquals('foo', $signedJwt->getSignature());
    }

    /**
     * Tests creating a JWT from a string with the "none" algorithm
     */
    public function testCreatingJwtFromStringWithNoneAlgorithm(): void
    {
        $header = new JwtHeader('none');
        $unsignedJwt = new UnsignedJwt($header, new JwtPayload());
        $signedJwt = SignedJwt::createFromString($unsignedJwt->getUnsignedValue());
        $this->assertEquals('none', $signedJwt->getHeader()->getAlgorithm());
        $this->assertEquals('', $signedJwt->getSignature());
    }

    public function testDecodingEncodedToken(): void
    {
        $signer = new HmacSigner(Algorithms::SHA256, 'public');
        $unsignedJwt = new UnsignedJwt($this->header, $this->payload);
        $signature = $signer->sign($unsignedJwt->getUnsignedValue());
        $signedJwt = new SignedJwt($this->header, $this->payload, $signature);
        $token = $signedJwt->encode();
        $this->assertTokensEqual($signedJwt, SignedJwt::createFromString($token), false);
    }

    public function testEncodingDecodingRsaAlgorithms(): void
    {
        $algorithms = [
            Algorithms::RSA_SHA256 => 'sha256',
            Algorithms::RSA_SHA384 => 'sha384',
            Algorithms::RSA_SHA512 => 'sha512'
        ];

        foreach ($algorithms as $algorithm => $digestAlgorithm) {
            $privateKey = openssl_pkey_new(
                [
                    'digest_alg' => $digestAlgorithm,
                    'private_key_bits' => 1024,
                    'private_key_type' => OPENSSL_KEYTYPE_RSA
                ]
            );
            $publicKey = openssl_pkey_get_details($privateKey);
            $signer = new RsaSsaPkcsSigner($algorithm, $publicKey['key'], $privateKey);
            $unsignedJwt = new UnsignedJwt($this->header, $this->payload);
            $unsignedJwt->getHeader()->add('alg', $algorithm);
            $signature = $signer->sign($unsignedJwt->getUnsignedValue());
            $signedJwt = new SignedJwt($this->header, $this->payload, $signature);
            $token = $signedJwt->encode();
            $this->assertTokensEqual($signedJwt, SignedJwt::createFromString($token), false);
        }
    }

    public function testExceptionThrownWithInvalidNumberSegments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        SignedJwt::createFromString('foo.bar');
    }

    public function testExceptionThrownWithNoAlgorithmSet(): void
    {
        $this->expectException(InvalidArgumentException::class);
        SignedJwt::createFromString(base64_encode('foo') . '.' . base64_encode('bar') . '.' . base64_encode('baz'));
    }

    public function testGettingSignature(): void
    {
        $jwt = new SignedJwt($this->header, $this->payload, 'signature');
        $this->assertEquals('signature', $jwt->getSignature());
    }

    /**
     * Asserts whether two tokens are equal
     *
     * @param SignedJwt $a The first token
     * @param SignedJwt $b The second token
     * @param bool $checkSignature Whether or not to check the signatures
     */
    private function assertTokensEqual(SignedJwt $a, SignedJwt $b, bool $checkSignature): void
    {
        // Because the JTI is random for each payload, exclude it
        $payloadA = $a->getPayload()->getAll();
        $payloadB = $b->getPayload()->getAll();
        unset($payloadA['jti'], $payloadB['jti']);
        $this->assertEquals($a->getHeader(), $b->getHeader());
        $this->assertEquals($payloadA, $payloadB);

        if ($checkSignature) {
            $this->assertEquals($a->getSignature(), $b->getSignature());
        }
    }
}

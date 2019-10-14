<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\TestsTemp\Tokens\JsonWebTokens\Verification;

use Opulence\Authentication\Tokens\JsonWebTokens\JwtHeader;
use Opulence\Authentication\Tokens\JsonWebTokens\JwtPayload;
use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\JwtVerifier;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\VerificationContext;
use Opulence\Authentication\Tokens\Signatures\Algorithms;
use Opulence\Authentication\Tokens\Signatures\ISigner;

/**
 * Tests the JWT verifier
 */
class JwtVerifierTest extends \PHPUnit\Framework\TestCase
{
    private JwtVerifier $verifier;
    /** @var ISigner The signer to use in tests */
    private ISigner $signer;
    /** @var VerificationContext The verification context to use in tests */
    private VerificationContext $context;

    protected function setUp(): void
    {
        $this->verifier = new JwtVerifier();
        $this->signer = $this->createMock(ISigner::class);
        $this->signer->expects($this->any())
            ->method('getAlgorithm')
            ->willReturn(Algorithms::SHA256);
        $this->signer->expects($this->any())
            ->method('verify')
            ->willReturn(true);
        $this->context = new VerificationContext($this->signer);
    }

    public function testVerifyingValidToken(): void
    {
        $jwt = new SignedJwt(new JwtHeader(), new JwtPayload(), 'signature');
        $errors = [];
        $this->assertTrue($this->verifier->verify($jwt, $this->context, $errors));
        $this->assertEquals([], $errors);
    }
}

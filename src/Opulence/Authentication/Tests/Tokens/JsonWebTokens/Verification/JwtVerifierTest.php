<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Tests\Tokens\JsonWebTokens\Verification;

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
    /** @var JwtVerifier The verifier to use in tests */
    private $verifier;
    /** @var ISigner The signer to use in tests */
    private $signer;
    /** @var VerificationContext The verification context to use in tests */
    private $context;

    /**
     * Sets up the tests
     */
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

    /**
     * Tests verifying a valid token
     */
    public function testVerifyingValidToken(): void
    {
        $jwt = new SignedJwt(new JwtHeader(), new JwtPayload(), 'signature');
        $errors = [];
        $this->assertTrue($this->verifier->verify($jwt, $this->context, $errors));
        $this->assertEquals([], $errors);
    }
}

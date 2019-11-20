<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Tests\Credentials;

use DateInterval;
use DateTimeImmutable;
use Opulence\Authentication\Credentials\RefreshTokenCredentialFactory;
use Opulence\Authentication\IPrincipal;
use Opulence\Authentication\ISubject;
use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;
use Opulence\Authentication\Tokens\Signatures\ISigner;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests the refresh credential factory
 */
class RefreshTokenCredentialFactoryTest extends TestCase
{
    private RefreshTokenCredentialFactory $factory;
    /** @var ISigner|MockObject The signer to use in tests */
    private ISigner $signer;
    /** @var ISubject|MockObject The subject to use in tests */
    private ISubject $subject;

    protected function setUp(): void
    {
        $this->signer = $this->createMock(ISigner::class);
        $this->signer->expects($this->any())
            ->method('sign')
            ->willReturn('signed');
        $this->subject = $this->createMock(ISubject::class);
        $principal = $this->createMock(IPrincipal::class);
        $principal->expects($this->any())
            ->method('getId')
            ->willReturn('principalId');
        $this->subject->expects($this->any())
            ->method('getPrimaryPrincipal')
            ->willReturn($principal);
        $this->factory = new RefreshTokenCredentialFactory(
            $this->signer,
            'foo',
            'bar',
            new DateInterval('P0D'),
            new DateInterval('P1Y')
        );
    }

    public function testClaimsAdded(): void
    {
        $credential = $this->factory->createCredentialForSubject($this->subject);
        $tokenString = $credential->getValue('token');
        /** @var SignedJwt $signedJwt */
        $signedJwt = SignedJwt::createFromString($tokenString);
        $payload = $signedJwt->getPayload();
        $this->assertEquals('foo', $payload->getIssuer());
        $this->assertEquals('bar', $payload->getAudience());
        $this->assertEquals('principalId', $payload->getSubject());
        $this->assertEquals((new DateTimeImmutable)->format('Y'), $payload->getValidFrom()->format('Y'));
        $this->assertEquals((new DateTimeImmutable('+1 year'))->format('Y'), $payload->getValidTo()->format('Y'));
        $this->assertEquals((new DateTimeImmutable)->format('Y'), $payload->getIssuedAt()->format('Y'));
    }
}

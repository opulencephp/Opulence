<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authentication\Tests\Credentials\Factories;

use DateInterval;
use DateTimeImmutable;
use Opulence\Authentication\Credentials\Factories\AccessTokenCredentialFactory;
use Opulence\Authentication\IPrincipal;
use Opulence\Authentication\ISubject;
use Opulence\Authentication\Roles\Orm\IRoleRepository;
use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;
use Opulence\Authentication\Tokens\Signatures\ISigner;
use Opulence\Authentication\Users\IUser;
use Opulence\Authentication\Users\Orm\IUserRepository;

/**
 * Tests the access credential factory
 */
class AccessTokenCredentialFactoryTest extends \PHPUnit\Framework\TestCase
{
    /** @var AccessTokenCredentialFactory The factory to use in tests */
    private $factory = null;
    /** @var IUserRepository|\PHPUnit_Framework_MockObject_MockObject The user repository to use in tests */
    private $userRepository = null;
    /** @var IRoleRepository|\PHPUnit_Framework_MockObject_MockObject The role repository to use in tests */
    private $roleRepository = null;
    /** @var ISigner|\PHPUnit_Framework_MockObject_MockObject The signer to use in tests */
    private $signer = null;
    /** @var ISubject|\PHPUnit_Framework_MockObject_MockObject The subject to use in tests */
    private $subject = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->signer = $this->createMock(ISigner::class);
        $this->signer->expects($this->any())
            ->method('sign')
            ->willReturn('signed');
        $user = $this->createMock(IUser::class);
        $user->expects($this->any())
            ->method('getUsername')
            ->willReturn('Dave');
        $this->userRepository = $this->createMock(IUserRepository::class);
        $this->userRepository->expects($this->any())
            ->method('getById')
            ->with('principalId')
            ->willReturn($user);
        $this->roleRepository = $this->createMock(IRoleRepository::class);
        $this->roleRepository->expects($this->any())
            ->method('getRoleNamesForSubject')
            ->with('principalId')
            ->willReturn(['role1', 'role2']);
        $this->subject = $this->createMock(ISubject::class);
        $principal = $this->createMock(IPrincipal::class);
        $principal->expects($this->any())
            ->method('getId')
            ->willReturn('principalId');
        $this->subject->expects($this->any())
            ->method('getPrimaryPrincipal')
            ->willReturn($principal);
        $this->factory = new AccessTokenCredentialFactory(
            $this->userRepository,
            $this->roleRepository,
            $this->signer,
            'foo',
            'bar',
            'baz',
            new DateInterval('P0D'),
            new DateInterval('P1Y')
        );
    }

    /**
     * Tests that the claims are added
     */
    public function testClaimsAdded()
    {
        $credential = $this->factory->createCredentialForSubject($this->subject);
        $tokenString = $credential->getValue('token');
        /** @var SignedJwt $signedJwt */
        $signedJwt = SignedJwt::createFromString($tokenString);
        $payload = $signedJwt->getPayload();
        $this->assertEquals('foo', $payload->getIssuer());
        $this->assertEquals(['bar', 'baz'], $payload->getAudience());
        $this->assertEquals('principalId', $payload->getSubject());
        $this->assertEquals((new DateTimeImmutable)->format('Y'), $payload->getValidFrom()->format('Y'));
        $this->assertEquals((new DateTimeImmutable('+1 year'))->format('Y'), $payload->getValidTo()->format('Y'));
        $this->assertEquals((new DateTimeImmutable)->format('Y'), $payload->getIssuedAt()->format('Y'));
        $this->assertEquals(['role1', 'role2'], $payload->get('roles'));
        $this->assertEquals('Dave', $payload->get('user')['username']);
    }
}

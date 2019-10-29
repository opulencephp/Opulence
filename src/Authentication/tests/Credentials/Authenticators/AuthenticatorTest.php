<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Tests\Credentials\Authenticators;

use InvalidArgumentException;
use Opulence\Authentication\Credentials\Authenticators\Authenticator;
use Opulence\Authentication\Credentials\Authenticators\IAuthenticator;
use Opulence\Authentication\Credentials\Authenticators\IAuthenticatorRegistry;
use Opulence\Authentication\Credentials\ICredential;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests the authenticator
 */
class AuthenticatorTest extends TestCase
{
    private Authenticator $authenticator;
    /** @var IAuthenticatorRegistry|MockObject The authenticator registry to use in tests */
    private IAuthenticatorRegistry $authenticatorRegistry;

    protected function setUp(): void
    {
        $this->authenticatorRegistry = $this->createMock(IAuthenticatorRegistry::class);
        $this->authenticator = new Authenticator($this->authenticatorRegistry);
    }

    public function testAuthenticatorThatSuccessfullyAuthenticatesCredential(): void
    {
        /** @var ICredential|MockObject $credential */
        $credential = $this->createMock(ICredential::class);
        $credential->expects($this->once())
            ->method('getType')
            ->willReturn('foo');
        $subject = null;
        $actualAuthenticator = $this->createMock(IAuthenticator::class);
        $actualAuthenticator->expects($this->once())
            ->method('authenticate')
            ->with($credential, $subject)
            ->willReturn(true);
        $this->authenticatorRegistry->expects($this->once())
            ->method('getAuthenticators')
            ->with('foo')
            ->willReturn([$actualAuthenticator]);
        $this->assertTrue($this->authenticator->tryAuthenticate($credential, $subject));
    }

    public function testAuthenticatorThatUnsuccessfullyAuthenticatesCredential(): void
    {
        /** @var ICredential|MockObject $credential */
        $credential = $this->createMock(ICredential::class);
        $credential->expects($this->once())
            ->method('getType')
            ->willReturn('foo');
        $subject = null;
        $actualAuthenticator = $this->createMock(IAuthenticator::class);
        $actualAuthenticator->expects($this->once())
            ->method('authenticate')
            ->with($credential, $subject)
            ->willReturn(false);
        $this->authenticatorRegistry->expects($this->once())
            ->method('getAuthenticators')
            ->with('foo')
            ->willReturn([$actualAuthenticator]);
        $this->assertFalse($this->authenticator->tryAuthenticate($credential, $subject));
    }

    public function testExceptionThrownWithNoAuthenticatorForCredential(): void
    {
        $this->expectException(InvalidArgumentException::class);
        /** @var ICredential|MockObject $credential */
        $credential = $this->createMock(ICredential::class);
        $credential->expects($this->once())
            ->method('getType')
            ->willReturn('foo');
        $subject = null;
        $this->authenticatorRegistry->expects($this->once())
            ->method('getAuthenticators')
            ->with('foo')
            ->willThrowException(new InvalidArgumentException);
        $this->authenticator->tryAuthenticate($credential, $subject);
    }
}

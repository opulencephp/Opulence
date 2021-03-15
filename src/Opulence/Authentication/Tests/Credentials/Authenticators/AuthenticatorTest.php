<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authentication\Tests\Credentials\Authenticators;

use InvalidArgumentException;
use Opulence\Authentication\Credentials\Authenticators\Authenticator;
use Opulence\Authentication\Credentials\Authenticators\IAuthenticator;
use Opulence\Authentication\Credentials\Authenticators\IAuthenticatorRegistry;
use Opulence\Authentication\Credentials\ICredential;

/**
 * Tests the authenticator
 */
class AuthenticatorTest extends \PHPUnit\Framework\TestCase
{
    /** @var Authenticator The authenticator to use in tests */
    private $authenticator = null;
    /** @var IAuthenticatorRegistry|\PHPUnit_Framework_MockObject_MockObject The authenticator registry to use in tests */
    private $authenticatorRegistry = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->authenticatorRegistry = $this->createMock(IAuthenticatorRegistry::class);
        $this->authenticator = new Authenticator($this->authenticatorRegistry);
    }

    /**
     * Tests an authenticator that successfully authenticates a credential
     */
    public function testAuthenticatorThatSuccessfullyAuthenticatesCredential()
    {
        /** @var ICredential|\PHPUnit_Framework_MockObject_MockObject $credential */
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
        $this->assertTrue($this->authenticator->authenticate($credential, $subject));
    }

    /**
     * Tests an authenticator that unsuccessfully authenticates a credential
     */
    public function testAuthenticatorThatUnsuccessfullyAuthenticatesCredential()
    {
        /** @var ICredential|\PHPUnit_Framework_MockObject_MockObject $credential */
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
        $this->assertFalse($this->authenticator->authenticate($credential, $subject));
    }

    /**
     * Tests that an exception is thrown with no authenticator for a credential
     */
    public function testExceptionThrownWithNoAuthenticatorForCredential()
    {
        $this->expectException(InvalidArgumentException::class);
        /** @var ICredential|\PHPUnit_Framework_MockObject_MockObject $credential */
        $credential = $this->createMock(ICredential::class);
        $credential->expects($this->once())
            ->method('getType')
            ->willReturn('foo');
        $subject = null;
        $this->authenticatorRegistry->expects($this->once())
            ->method('getAuthenticators')
            ->with('foo')
            ->willThrowException(new InvalidArgumentException);
        $this->authenticator->authenticate($credential, $subject);
    }
}

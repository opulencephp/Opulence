<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Credentials\Authenticators;

use InvalidArgumentException;
use Opulence\Authentication\Credentials\ICredential;

/**
 * Tests the authenticator
 */
class AuthenticatorTest extends \PHPUnit_Framework_TestCase
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
        $this->authenticatorRegistry = $this->getMock(IAuthenticatorRegistry::class);
        $this->authenticator = new Authenticator($this->authenticatorRegistry);
    }

    /**
     * Tests an authenticator that successfully authenticates a credential
     */
    public function testAuthenticatorThatSuccessfullyAuthenticatesCredential()
    {
        /** @var ICredential|\PHPUnit_Framework_MockObject_MockObject $credential */
        $credential = $this->getMock(ICredential::class);
        $credential->expects($this->once())
            ->method("getType")
            ->willReturn("foo");
        $user = null;
        $actualAuthenticator = $this->getMock(IAuthenticator::class);
        $actualAuthenticator->expects($this->once())
            ->method("authenticate")
            ->with($credential, $user)
            ->willReturn(true);
        $this->authenticatorRegistry->expects($this->once())
            ->method("getAuthenticator")
            ->with("foo")
            ->willReturn($actualAuthenticator);
        $this->assertTrue($this->authenticator->authenticate($credential, $user));
    }

    /**
     * Tests an authenticator that unsuccessfully authenticates a credential
     */
    public function testAuthenticatorThatUnsuccessfullyAuthenticatesCredential()
    {
        /** @var ICredential|\PHPUnit_Framework_MockObject_MockObject $credential */
        $credential = $this->getMock(ICredential::class);
        $credential->expects($this->once())
            ->method("getType")
            ->willReturn("foo");
        $user = null;
        $actualAuthenticator = $this->getMock(IAuthenticator::class);
        $actualAuthenticator->expects($this->once())
            ->method("authenticate")
            ->with($credential, $user)
            ->willReturn(false);
        $this->authenticatorRegistry->expects($this->once())
            ->method("getAuthenticator")
            ->with("foo")
            ->willReturn($actualAuthenticator);
        $this->assertFalse($this->authenticator->authenticate($credential, $user));
    }

    /**
     * Tests that an exception is thrown with no authenticator for a credential
     */
    public function testExceptionThrownWithNoAuthenticatorForCredential()
    {
        $this->expectException(InvalidArgumentException::class);
        /** @var ICredential|\PHPUnit_Framework_MockObject_MockObject $credential */
        $credential = $this->getMock(ICredential::class);
        $credential->expects($this->once())
            ->method("getType")
            ->willReturn("foo");
        $user = null;
        $this->authenticatorRegistry->expects($this->once())
            ->method("getAuthenticator")
            ->with("foo")
            ->willThrowException(new InvalidArgumentException);
        $this->authenticator->authenticate($credential, $user);
    }
}
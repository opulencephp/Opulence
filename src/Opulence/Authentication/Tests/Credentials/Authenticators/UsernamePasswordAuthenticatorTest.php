<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authentication\Tests\Credentials\Authenticators;

use Opulence\Authentication\Credentials\Authenticators\AuthenticatorErrorTypes;
use Opulence\Authentication\Credentials\Authenticators\UsernamePasswordAuthenticator;
use Opulence\Authentication\Credentials\ICredential;
use Opulence\Authentication\ISubject;
use Opulence\Authentication\Roles\Orm\IRoleRepository;
use Opulence\Authentication\Users\IUser;
use Opulence\Authentication\Users\Orm\IUserRepository;

/**
 * Tests the username/password authenticator
 */
class UsernamePasswordAuthenticatorTest extends \PHPUnit\Framework\TestCase
{
    /** @var UsernamePasswordAuthenticator The authenticator to use in tests */
    private $authenticator = null;
    /** @var IUserRepository|\PHPUnit_Framework_MockObject_MockObject The user repository to use in tests */
    private $userRepository = null;
    /** @var IRoleRepository|\PHPUnit_Framework_MockObject_MockObject The role repository to use in tests */
    private $roleRepository = null;
    /** @var ICredential|\PHPUnit_Framework_MockObject_MockObject The credential to use in tests */
    private $credential = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->userRepository = $this->createMock(IUserRepository::class);
        $this->roleRepository = $this->createMock(IRoleRepository::class);
        $this->authenticator = new UsernamePasswordAuthenticator($this->userRepository, $this->roleRepository, 'pepper');
        $this->credential = $this->createMock(ICredential::class);
    }

    /**
     * Tests that a correct password returns true
     */
    public function testCorrectPasswordReturnsTrue()
    {
        $this->roleRepository->expects($this->once())
            ->method('getRoleNamesForSubject')
            ->willReturn(['role']);
        $this->credential->expects($this->at(0))
            ->method('getValue')
            ->with('username')
            ->willReturn('foo');
        $this->credential->expects($this->at(1))
            ->method('getValue')
            ->with('password')
            ->willReturn('password');
        $user = $this->createMock(IUser::class);
        $user->expects($this->once())
            ->method('getHashedPassword')
            ->willReturn(password_hash('password' . 'pepper', PASSWORD_BCRYPT));
        $user->expects($this->once())
            ->method('getId')
            ->willReturn('userId');
        $this->userRepository->expects($this->once())
            ->method('getByUsername')
            ->with('foo')
            ->willReturn($user);
        $subject = null;
        $this->assertTrue($this->authenticator->authenticate($this->credential, $subject));
        /** @var ISubject $subject */
        $this->assertInstanceOf(ISubject::class, $subject);
        $this->assertEquals('userId', $subject->getPrimaryPrincipal()->getId());
        $this->assertEquals(['role'], $subject->getPrimaryPrincipal()->getRoles());
        $this->assertEquals([$this->credential], $subject->getCredentials());
    }

    /**
     * Tests that an incorrect password returns false
     */
    public function testIncorrectPasswordReturnsFalse()
    {
        $this->credential->expects($this->at(0))
            ->method('getValue')
            ->with('username')
            ->willReturn('foo');
        $this->credential->expects($this->at(1))
            ->method('getValue')
            ->with('password')
            ->willReturn('bar');
        $user = $this->createMock(IUser::class);
        $user->expects($this->once())
            ->method('getHashedPassword')
            ->willReturn(password_hash('password', PASSWORD_BCRYPT));
        $this->userRepository->expects($this->once())
            ->method('getByUsername')
            ->with('foo')
            ->willReturn($user);
        $subject = null;
        $error = null;
        $this->assertFalse($this->authenticator->authenticate($this->credential, $subject, $error));
        $this->assertEquals(AuthenticatorErrorTypes::CREDENTIAL_INCORRECT, $error);
    }

    /**
     * Tests that a non-existent username returns false
     */
    public function testNonExistentUsernameReturnsFalse()
    {
        $this->credential->expects($this->at(0))
            ->method('getValue')
            ->with('username')
            ->willReturn('foo');
        $this->credential->expects($this->at(1))
            ->method('getValue')
            ->with('password')
            ->willReturn('bar');
        $this->userRepository->expects($this->once())
            ->method('getByUsername')
            ->with('foo')
            ->willReturn(null);
        $subject = null;
        $error = null;
        $this->assertFalse($this->authenticator->authenticate($this->credential, $subject, $error));
        $this->assertEquals(AuthenticatorErrorTypes::NO_SUBJECT, $error);
    }

    /**
     * Tests that an unset password credential returns false
     */
    public function testUnsetPasswordCredentialReturnsFalse()
    {
        $this->credential->expects($this->at(0))
            ->method('getValue')
            ->with('username')
            ->willReturn('foo');
        $this->credential->expects($this->at(1))
            ->method('getValue')
            ->with('password')
            ->willReturn(null);
        $subject = null;
        $error = null;
        $this->assertFalse($this->authenticator->authenticate($this->credential, $subject, $error));
        $this->assertEquals(AuthenticatorErrorTypes::CREDENTIAL_MISSING, $error);
    }

    /**
     * Tests that an unset username credential returns false
     */
    public function testUnsetUsernameCredentialReturnsFalse()
    {
        $this->credential->expects($this->at(0))
            ->method('getValue')
            ->with('username')
            ->willReturn(null);
        $this->credential->expects($this->at(1))
            ->method('getValue')
            ->with('password')
            ->willReturn('foo');
        $subject = null;
        $error = null;
        $this->assertFalse($this->authenticator->authenticate($this->credential, $subject, $error));
        $this->assertEquals(AuthenticatorErrorTypes::CREDENTIAL_MISSING, $error);
    }
}

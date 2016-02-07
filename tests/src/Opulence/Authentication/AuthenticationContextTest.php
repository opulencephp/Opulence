<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication;

/**
 * Tests the authentication context
 */
class AuthenticationContextTest extends \PHPUnit_Framework_TestCase
{
    /** @var AuthenticationContext The context to use in tests */
    private $context = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->context = new AuthenticationContext();
    }

    /**
     * Tests checking if the user is authenticated
     */
    public function testCheckingIfAuthenticated()
    {
        $this->context->setStatus(AuthenticationStatusTypes::AUTHENTICATED);
        $this->assertTrue($this->context->isAuthenticated());
        $this->context->setStatus(AuthenticationStatusTypes::UNAUTHENTICATED);
        $this->assertFalse($this->context->isAuthenticated());
    }

    /**
     * Tests setting the status in the constructor
     */
    public function testSettingStatusInConstructor()
    {
        $context = new AuthenticationContext(null, 23);
        $this->assertEquals(23, $context->getStatus());
    }

    /**
     * Tests setting the status in the setter
     */
    public function testSettingStatusInSetter()
    {
        $this->context->setStatus(23);
        $this->assertEquals(23, $this->context->getStatus());
    }

    /**
     * Tests setting the user in the constructor
     */
    public function testSettingUserInConstructor()
    {
        /** @var IAuthenticatable $user */
        $user = $this->getMock(IAuthenticatable::class);
        $context = new AuthenticationContext($user);
        $this->assertSame($user, $context->getUser());
    }

    /**
     * Tests setting the user in the setter
     */
    public function testSettingUserInSetter()
    {
        /** @var IAuthenticatable $user */
        $user = $this->getMock(IAuthenticatable::class);
        $this->context->setUser($user);
        $this->assertSame($user, $this->context->getUser());
    }
}
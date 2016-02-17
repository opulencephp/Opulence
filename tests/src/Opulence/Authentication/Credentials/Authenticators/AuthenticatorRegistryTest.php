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

/**
 * Tests the authenticator registry
 */
class AuthenticatorRegistryTest extends \PHPUnit_Framework_TestCase
{
    /** @var AuthenticatorRegistry The registry to use in tests */
    private $registry = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->registry = new AuthenticatorRegistry();
    }

    /**
     * Tests that the correct authenticator is returned
     */
    public function testCorrectAuthenticatorReturned()
    {
        /** @var IAuthenticator $authenticator */
        $authenticator = $this->getMock(IAuthenticator::class);
        $this->registry->registerAuthenticator("foo", $authenticator);
        $this->assertSame($authenticator, $this->registry->getAuthenticator("foo"));
    }

    /**
     * Tests that an exception is thrown with a non-existent authenticator
     */
    public function testExceptionThrownOnNonExistentAuthenticator()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->registry->getAuthenticator("foo");
    }
}
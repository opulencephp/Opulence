<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Credentials\Authenticators;

use InvalidArgumentException;

/**
 * Tests the authenticator registry
 */
class AuthenticatorRegistryTest extends \PHPUnit\Framework\TestCase
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
        $authenticator = $this->createMock(IAuthenticator::class);
        $this->registry->registerAuthenticator('foo', $authenticator);
        $this->assertEquals([$authenticator], $this->registry->getAuthenticators('foo'));
    }

    /**
     * Tests that an exception is thrown with a non-existent authenticator
     */
    public function testExceptionThrownOnNonExistentAuthenticator()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->registry->getAuthenticators('foo');
    }
}

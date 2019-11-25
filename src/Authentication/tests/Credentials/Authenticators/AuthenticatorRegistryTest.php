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

use Opulence\Authentication\Credentials\Authenticators\AuthenticatorRegistry;
use Opulence\Authentication\Credentials\Authenticators\IAuthenticator;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

/**
 * Tests the authenticator registry
 */
class AuthenticatorRegistryTest extends TestCase
{
    private AuthenticatorRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = new AuthenticatorRegistry();
    }

    public function testCorrectAuthenticatorReturned(): void
    {
        /** @var IAuthenticator $authenticator */
        $authenticator = $this->createMock(IAuthenticator::class);
        $this->registry->registerAuthenticator('foo', $authenticator);
        $this->assertEquals([$authenticator], $this->registry->getAuthenticators('foo'));
    }

    /**
     * Tests that an exception is thrown with a non-existent authenticator
     */
    public function testExceptionThrownOnNonExistentAuthenticator(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->registry->getAuthenticators('foo');
    }
}

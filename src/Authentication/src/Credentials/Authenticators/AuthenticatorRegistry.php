<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Credentials\Authenticators;

use OutOfBoundsException;

/**
 * Defines the authenticator registry
 */
final class AuthenticatorRegistry implements IAuthenticatorRegistry
{
    /** @var array The mapping of credential types to authenticators */
    private array $credentialTypesToAuthenticators = [];

    /**
     * @inheritdoc
     */
    public function getAuthenticators(string $credentialType): array
    {
        if (!isset($this->credentialTypesToAuthenticators[$credentialType])) {
            throw new OutOfBoundsException("No authenticator registered for credential type $credentialType");
        }

        return $this->credentialTypesToAuthenticators[$credentialType];
    }

    /**
     * @inheritdoc
     */
    public function registerAuthenticator(string $credentialType, IAuthenticator $authenticator): void
    {
        if (!isset($this->credentialTypesToAuthenticators[$credentialType])) {
            $this->credentialTypesToAuthenticators[$credentialType] = [];
        }

        $this->credentialTypesToAuthenticators[$credentialType][] = $authenticator;
    }
}

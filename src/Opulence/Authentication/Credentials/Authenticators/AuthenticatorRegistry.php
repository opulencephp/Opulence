<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Authentication\Credentials\Authenticators;

use InvalidArgumentException;

/**
 * Defines the authenticator registry
 */
class AuthenticatorRegistry implements IAuthenticatorRegistry
{
    /** @var array The mapping of credential types to authenticators */
    private $credentialTypesToAuthenticators = [];

    /**
     * @inheritdoc
     */
    public function getAuthenticators(string $credentialType) : array
    {
        if (!isset($this->credentialTypesToAuthenticators[$credentialType])) {
            throw new InvalidArgumentException("No authenticator registered for credential type $credentialType");
        }

        return $this->credentialTypesToAuthenticators[$credentialType];
    }

    /**
     * @inheritdoc
     */
    public function registerAuthenticator(string $credentialType, IAuthenticator $authenticator)
    {
        if (!isset($this->credentialTypesToAuthenticators[$credentialType])) {
            $this->credentialTypesToAuthenticators[$credentialType] = [];
        }

        $this->credentialTypesToAuthenticators[$credentialType][] = $authenticator;
    }
}

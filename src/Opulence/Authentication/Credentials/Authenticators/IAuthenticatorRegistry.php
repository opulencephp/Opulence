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
 * Defines the interface for authenticator registries to implement
 */
interface IAuthenticatorRegistry
{
    /**
     * Gets the authenticators for a credential type
     *
     * @param string $credentialType The credential type whose authenticator we want
     * @return IAuthenticator[] The list of authenticators for the input credential type
     * @throws InvalidArgumentException Thrown if no authenticator was registered for the credential type
     */
    public function getAuthenticators(string $credentialType) : array;

    /**
     * Registers an authenticator for the input credential type
     *
     * @param string $credentialType The credential type
     * @param IAuthenticator $authenticator The authenticator to register
     */
    public function registerAuthenticator(string $credentialType, IAuthenticator $authenticator);
}

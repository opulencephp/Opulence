<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication;

use InvalidArgumentException;

/**
 * Defines the authenticator registry
 */
class AuthenticatorRegistry
{
    /** @var array The mapping of credential types to authenticators */
    private $credentialTypesToAuthenticators = [];

    /**
     * Gets the authenticator for a credential type
     *
     * @param int $credentialTypeId The credential type whose authenticator we want
     * @return IAuthenticator The authenticator for the input credential type
     * @throws InvalidArgumentException Thrown if no authenticator was registered for the credential type
     */
    public function getAuthenticator(int $credentialTypeId) : IAuthenticator
    {
        if (!isset($this->credentialTypesToAuthenticators[$credentialTypeId])) {
            throw new InvalidArgumentException("No authenticator registered for credential type $credentialTypeId");
        }

        return $this->credentialTypesToAuthenticators[$credentialTypeId];
    }

    /**
     * Registers an authenticator for the input credential type
     *
     * @param int $credentialTypeId The credential type
     * @param IAuthenticator $authenticator The authenticator to register
     */
    public function registerAuthenticator(int $credentialTypeId, IAuthenticator $authenticator)
    {
        $this->credentialTypesToAuthenticators[$credentialTypeId] = $authenticator;
    }
}
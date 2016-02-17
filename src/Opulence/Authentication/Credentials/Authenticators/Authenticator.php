<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Credentials\Authenticators;

use Opulence\Authentication\Credentials\ICredential;
use Opulence\Authentication\IAuthenticatable;

/**
 * Defines an authenticator that can be used to authenticate all credential types
 */
class Authenticator implements IAuthenticator
{
    /** @var IAuthenticatorRegistry The authenticator registry */
    protected $authenticatorRegistry = null;

    /**
     * @param IAuthenticatorRegistry $authenticatorRegistry The authenticator registry
     */
    public function __construct(IAuthenticatorRegistry $authenticatorRegistry)
    {
        $this->authenticatorRegistry = $authenticatorRegistry;
    }

    /**
     * @inheritdoc
     */
    public function authenticate(ICredential $credential, IAuthenticatable &$user = null) : bool
    {
        $authenticator = $this->authenticatorRegistry->getAuthenticator($credential->getType());

        return $authenticator->authenticate($credential, $user);
    }
}
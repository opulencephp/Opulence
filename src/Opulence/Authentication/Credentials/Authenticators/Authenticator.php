<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authentication\Credentials\Authenticators;

use Opulence\Authentication\Credentials\ICredential;
use Opulence\Authentication\ISubject;

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
    public function authenticate(ICredential $credential, ISubject &$subject = null, string &$error = null) : bool
    {
        $authenticators = $this->authenticatorRegistry->getAuthenticators($credential->getType());
        $allAuthenticated = true;

        foreach ($authenticators as $authenticator) {
            if (!$authenticator->authenticate($credential, $subject, $error)) {
                $allAuthenticated = false;
                break;
            }
        }

        return $allAuthenticated;
    }
}

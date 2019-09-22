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

use Opulence\Authentication\Credentials\ICredential;
use Opulence\Authentication\ISubject;

/**
 * Defines an authenticator that can be used to authenticate all credential types
 */
class Authenticator implements IAuthenticator
{
    /** @var IAuthenticatorRegistry The authenticator registry */
    protected IAuthenticatorRegistry $authenticators;

    /**
     * @param IAuthenticatorRegistry $authenticatorRegistry The authenticator registry
     */
    public function __construct(IAuthenticatorRegistry $authenticatorRegistry)
    {
        $this->authenticators = $authenticatorRegistry;
    }

    /**
     * @inheritdoc
     */
    public function authenticate(ICredential $credential, ISubject &$subject = null, string &$error = null): bool
    {
        $authenticators = $this->authenticators->getAuthenticators($credential->getType());
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

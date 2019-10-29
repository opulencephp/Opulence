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
 * Defines the interface for authenticators to implement
 */
interface IAuthenticator
{
    /**
     * Authenticates a list of credentials
     *
     * @param ICredential $credential The credential to authenticate
     * @param ISubject $subject The authenticated subject, if one was found (used as an "out" parameter)
     * @param string $error The error type, if there was one
     * @return bool True if the credential is authentic, otherwise false
     */
    public function tryAuthenticate(ICredential $credential, ISubject &$subject = null, string &$error = null): bool;
}

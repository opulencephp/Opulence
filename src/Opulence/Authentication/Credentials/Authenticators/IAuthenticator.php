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
 * Defines the interface for authenticators to implement
 */
interface IAuthenticator
{
    /**
     * Authenticates a list of credentials
     *
     * @param ICredential $credential The credential to authenticate
     * @param IAuthenticatable $user The authenticated user, if one was found (used as an "out" parameter)
     * @return bool True if the credential is authentic, otherwise false
     */
    public function authenticate(ICredential $credential, IAuthenticatable &$user = null) : bool;
}
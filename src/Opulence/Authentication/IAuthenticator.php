<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication;

/**
 * Defines the interface for authenticators to implement
 */
interface IAuthenticator
{
    /**
     * Authenticates a list of credentials
     *
     * @param array $credentials The list of credentials to authenticate
     * @param IAuthenticatable $user The authenticated user, if one was found (used as an "out" parameter)
     * @return bool True if the credentials are authentic, otherwise false
     */
    public function authenticate(array $credentials, IAuthenticatable &$user = null) : bool;
}
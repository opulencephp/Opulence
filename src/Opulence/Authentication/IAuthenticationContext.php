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
 * Defines the interface for authentication contexts to implement
 */
interface IAuthenticationContext
{
    /**
     * Gets the current authentication status
     *
     * @return string The current authentication status
     */
    public function getStatus() : string;

    /**
     * Gets the current user if there is one
     *
     * @return IAuthenticatable|null The current user if there is one, otherwise null
     */
    public function getUser();

    /**
     * Gets whether or not the current user has been authenticated
     *
     * @return bool True if the current user is authenticated, otherwise false
     */
    public function isAuthenticated() : bool;

    /**
     * Sets the current status
     *
     * @param string $status The current status
     */
    public function setStatus(string $status);

    /**
     * Sets the current user
     *
     * @param IAuthenticatable $user The current user
     */
    public function setUser(IAuthenticatable $user);
}
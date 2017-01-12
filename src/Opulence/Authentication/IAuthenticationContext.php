<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
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
     * Gets the current subject, if there is one
     *
     * @return ISubject|null The current subject, if there is one, otherwise null
     */
    public function getSubject();

    /**
     * Gets whether or not the current subject has been authenticated
     *
     * @return bool True if the current subject is authenticated, otherwise false
     */
    public function isAuthenticated() : bool;

    /**
     * Sets the current status
     *
     * @param string $status The current status
     */
    public function setStatus(string $status);

    /**
     * Sets the current subject
     *
     * @param ISubject $subject The current subject
     */
    public function setSubject(ISubject $subject);
}

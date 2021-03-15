<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authentication;

/**
 * Defines the interface for principals to implement
 */
interface IPrincipal
{
    /**
     * Gets the identity
     *
     * @return mixed The identity of the principal
     */
    public function getId();

    /**
     * Gets the list of role names
     *
     * @return array The list of role names
     */
    public function getRoles() : array;

    /**
     * Gets the type of principal this is
     *
     * @return string The type
     */
    public function getType() : string;

    /**
     * Checks if a principal has a role
     *
     * @param string $roleName The name of the role to check
     * @return bool True if the principal has the role, otherwise false
     */
    public function hasRole(string $roleName) : bool;
}

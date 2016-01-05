<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Users;

use DateTime;

/**
 * Defines the user interface
 */
interface IUser
{
    /**
     * Gets the date the user was created
     *
     * @return DateTime The date the user was created
     */
    public function getDateCreated();

    /**
     * Gets the database Id
     *
     * @return int|string The database Id
     */
    public function getId();

    /**
     * Gets the list of this user's roles
     *
     * @return array
     */
    public function getRoles();

    /**
     * Gets whether or not a user has a particular role
     *
     * @param mixed $role The role to search for
     * @return bool True if the user has the role, otherwise false
     */
    public function hasRole($role);

    /**
     * Sets the database Id of the user
     *
     * @param int|string $id The database Id
     */
    public function setId($id);
} 
<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Users;

use DateTimeImmutable;

/**
 * Defines the user interface
 */
interface IUser
{
    /**
     * Gets the date the user was created
     *
     * @return DateTimeImmutable The date the user was created
     */
    public function getDateCreated() : DateTimeImmutable;

    /**
     * Gets the user's hashed password
     *
     * @return string The user's hashed password
     */
    public function getHashedPassword() : string;

    /**
     * Gets the database Id
     *
     * @return int|string The database Id
     */
    public function getId();

    /**
     * Gets the list of this user's roles
     *
     * @return array The list of rules
     */
    public function getRoles() : array;

    /**
     * Gets whether or not a user has a particular role
     *
     * @param mixed $role The role to search for
     * @return bool True if the user has the role, otherwise false
     */
    public function hasRole($role) : bool;

    /**
     * Sets the user's hashed password
     *
     * @param string $hashedPassword The user's hashed password
     */
    public function setHashedPassword(string $hashedPassword);

    /**
     * Sets the database Id of the user
     *
     * @param int|string $id The database Id
     */
    public function setId($id);
} 
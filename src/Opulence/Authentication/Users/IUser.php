<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Authentication\Users;

/**
 * Defines the interface for users
 */
interface IUser
{
    /**
     * Gets the entity's hashed password
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
     * Gets the user's username
     *
     * @return string The user's username
     */
    public function getUsername() : string;

    /**
     * Sets the entity's hashed password
     *
     * @param string $hashedPassword The user's hashed password
     */
    public function setHashedPassword(string $hashedPassword);

    /**
     * Sets the database Id of the entity
     *
     * @param int|string $id The database Id
     */
    public function setId($id);
}

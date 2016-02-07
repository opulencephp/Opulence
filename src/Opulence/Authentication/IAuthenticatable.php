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
 * Defines the interface for authenticatable entities
 */
interface IAuthenticatable
{
    /**
     * Gets the entity's hashed password
     *
     * @return string The entity's hashed password
     */
    public function getHashedPassword() : string;

    /**
     * Gets the database Id
     *
     * @return int|string The database Id
     */
    public function getId();

    /**
     * Sets the entity's hashed password
     *
     * @param string $hashedPassword The entity's hashed password
     */
    public function setHashedPassword(string $hashedPassword);

    /**
     * Sets the database Id of the entity
     *
     * @param int|string $id The database Id
     */
    public function setId($id);
}
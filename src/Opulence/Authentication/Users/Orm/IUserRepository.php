<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Users\Orm;

use Opulence\Authentication\Users\IUser;

/**
 * Defines the interface for user repositories
 */
interface IUserRepository
{
    /**
     * Adds a user to the repo
     *
     * @param IUser $user The user to add
     */
    public function add(&$user);

    /**
     * Deletes a user from the repo
     *
     * @param IUser $user The user to delete
     */
    public function delete(&$user);

    /**
     * Gets all the users
     *
     * @return IUser[] The list of all the users
     */
    public function getAll() : array;

    /**
     * Gets the user with the input Id
     *
     * @param int|string $id The user Id
     * @return IUser The user
     */
    public function getById($id);

    /**
     * Gets the user with the input username
     *
     * @param string $username The input username
     * @return IUser|null The user, if one was found, otherwise null
     */
    public function getByUsername(string $username);
}
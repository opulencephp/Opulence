<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Users\Orm;

use Opulence\Authentication\IAuthenticatable;

/**
 * Defines the interface for user repositories
 */
interface IUserRepository
{
    /**
     * Adds a user to the repo
     *
     * @param IAuthenticatable $user The user to add
     */
    public function add(&$user);

    /**
     * Deletes a user from the repo
     *
     * @param IAuthenticatable $user The user to delete
     */
    public function delete(&$user);

    /**
     * Gets all the users
     *
     * @return IAuthenticatable[] The list of all the users
     */
    public function getAll() : array;

    /**
     * Gets the user with the input Id
     *
     * @param int|string $id The user Id
     * @return IAuthenticatable|null The user, if one was found, otherwise null
     */
    public function getById($id);

    /**
     * Gets the user with the input Id and token
     *
     * @param int|string $userId The user Id
     * @param string $token The unhashed token to search by
     * @return IAuthenticatable|null The user, if one was found, otherwise null
     */
    public function getByToken($userId, string $token);
}
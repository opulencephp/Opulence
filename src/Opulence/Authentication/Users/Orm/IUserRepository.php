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
     * Gets the user with the input Id
     *
     * @param int|string $id The user Id
     * @return IAuthenticatable|null The user, if one was found, otherwise null
     */
    public function getById($id);

    /**
     * Gets the user with the input Id and token
     *
     * @param int|string $id The user Id
     * @param string $token The unhashed token to search by
     * @return IAuthenticatable|null The user, if one was found, otherwise null
     */
    public function getByToken($id, string $token);
}
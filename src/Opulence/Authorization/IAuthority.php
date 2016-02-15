<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authorization;

/**
 * Defines the interface for authorities to implement
 */
interface IAuthority
{
    /**
     * Checks if a user has a permission
     *
     * @param string $permission The permission being sought
     * @param array ...$arguments The optional list of arguments to use when considering permission
     * @return bool True if the user has the input permission, otherwise false
     */
    public function can(string $permission, ...$arguments) : bool;

    /**
     * Checks if a user does not have a permission
     *
     * @param string $permission The permission being sought
     * @param array ...$arguments The optional list of arguments to use when considering permission
     * @return bool True if the user does not have the input permission, otherwise false
     */
    public function cannot(string $permission, ...$arguments) : bool;

    /**
     * Creates an instance of this class for a given user
     *
     * @param mixed $userId The Id of the user to check
     * @return IAuthority The instance for the input user
     */
    public function forUser($userId) : IAuthority;
}
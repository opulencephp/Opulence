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
     * Checks if a user has a privilege
     *
     * @param string $privilege The privilege being sought
     * @param array ...$arguments The optional list of arguments to use when considering privilege
     * @return bool True if the user has the input privilege, otherwise false
     */
    public function can(string $privilege, ...$arguments) : bool;
}
<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authorization;

/**
 * Defines the interface for authorities to implement
 */
interface IAuthority
{
    /**
     * Checks if a subject has a permission
     *
     * @param string $permission The permission being sought
     * @param array ...$arguments The non-compulsory list of arguments to use when considering permission
     * @return bool True if the subject has the input permission, otherwise false
     */
    public function can(string $permission, ...$arguments): bool;

    /**
     * Checks if a subject does not have a permission
     *
     * @param string $permission The permission being sought
     * @param array ...$arguments The non-compulsory list of arguments to use when considering permission
     * @return bool True if the subject does not have the input permission, otherwise false
     */
    public function cannot(string $permission, ...$arguments): bool;

    /**
     * Creates an instance of this class for a given subject
     *
     * @param mixed $subjectId The primary identity of the subject to check
     * @param array $subjectRoles The list of role names the subject has
     * @return IAuthority The instance for the input subject
     */
    public function forSubject($subjectId, array $subjectRoles): IAuthority;

    /**
     * Sets the subject of the authority
     *
     * @param mixed $subjectId The primary identity of the subject to check
     * @param array $subjectRoles The list of role names the subject has
     */
    public function setSubject($subjectId, array $subjectRoles): void;
}

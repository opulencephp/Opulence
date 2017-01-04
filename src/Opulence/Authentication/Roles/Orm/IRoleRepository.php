<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Roles\Orm;

/**
 * Defines the interface for role repositories to implement
 * This allows you to use the Authentication library without necessarily having to use the Authorization library
 */
interface IRoleRepository
{
    /**
     * Gets the list of role names for a subject
     *
     * @param int|string $subjectId The Id of the subject whose roles we want
     * @return array The list of role names for the subject
     */
    public function getRoleNamesForSubject($subjectId) : array;
}

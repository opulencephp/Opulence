<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authorization\Roles;

/**
 * Defines a role membership
 */
class RoleMembership
{
    /** @var int|string The database Id */
    protected $id;
    /** @var int|string The subject Id */
    protected $subjectId;
    /** @var Role The role */
    protected Role $role;

    /**
     * @param int|string $id The database Id
     * @param int|string $subjectId The subject identity
     * @param Role $role The role
     */
    public function __construct($id, $subjectId, Role $role)
    {
        $this->id = $id;
        $this->subjectId = $subjectId;
        $this->role = $role;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Role
     */
    public function getRole(): Role
    {
        return $this->role;
    }

    /**
     * @return int|string
     */
    public function getSubjectId()
    {
        return $this->subjectId;
    }

    /**
     * @param int|string $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }
}

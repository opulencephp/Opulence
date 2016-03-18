<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authorization\Roles;

/**
 * Defines a role membership
 */
class RoleMembership
{
    /** @var int|string The database Id */
    protected $id = -1;
    /** @var int|string The subject Id */
    protected $subjectIdentity = -1;
    /** @var Role The role */
    protected $role = null;

    /**
     * @param int|string $id The database Id
     * @param int|string $subjectIdentity The subject identity
     * @param Role $role The role
     */
    public function __construct($id, $subjectIdentity, Role $role)
    {
        $this->id = $id;
        $this->subjectIdentity = $subjectIdentity;
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
    public function getRole() : Role
    {
        return $this->role;
    }

    /**
     * @return int|string
     */
    public function getSubjectIdentity()
    {
        return $this->subjectIdentity;
    }

    /**
     * @param int|string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
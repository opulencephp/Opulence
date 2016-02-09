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
    private $id = -1;
    /** @var int|string The user Id */
    private $userId = -1;
    /** @var Role The role */
    private $role = null;

    /**
     * @param int|string $id The database Id
     * @param int|string $userId The user Id
     * @param Role $role The role
     */
    public function __construct($id, $userId, Role $role)
    {
        $this->id = $id;
        $this->userId = $userId;
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
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int|string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
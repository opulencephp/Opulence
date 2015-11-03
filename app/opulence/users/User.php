<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Users;

use DateTime;

/**
 * Defines a user
 */
class User implements IUser
{
    /** @var int The database Id of the user */
    protected $id = -1;
    /** @var DateTime The date this user was created */
    protected $dateCreated = null;
    /** @var array The list of roles this user has */
    protected $roles = [];

    /**
     * @param int $id The database Id of this user
     * @param DateTime $dateCreated The date this user was created
     * @param array $roles The list of roles this user has
     */
    public function __construct($id, DateTime $dateCreated, array $roles = [])
    {
        $this->setId($id);
        $this->dateCreated = $dateCreated;
        $this->roles = $roles;
    }

    /**
     * @inheritdoc
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @inheritdoc
     */
    public function hasRole($role)
    {
        return in_array($role, $this->roles);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->id = $id;
    }
} 
<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Users;

use Opulence\Authentication\Authorization\IAuthorizable;
use Opulence\Authentication\IAuthenticatable;

/**
 * Defines a basic user
 */
class User implements IAuthenticatable, IAuthorizable
{
    /** @var int|string The database Id of the user */
    protected $id = -1;
    /** @var string The hashed password of the user */
    protected $hashedPassword = "";
    /** @var array The list of roles this user has */
    protected $roles = [];

    /**
     * @param int|string $id The database Id of this user
     * @param string $hashedPassword The hashed password of this user
     * @param array $roles The list of roles this user has
     */
    public function __construct($id, string $hashedPassword, array $roles = [])
    {
        $this->setId($id);
        $this->setHashedPassword($hashedPassword);

        // Convert the roles to a hash table for faster lookup
        foreach ($roles as $role) {
            $this->roles[$role] = true;
        }
    }

    /**
     * @inheritdoc
     */
    public function getHashedPassword() : string
    {
        return $this->hashedPassword;
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
    public function getRoles() : array
    {
        return array_keys($this->roles);
    }

    /**
     * @inheritdoc
     */
    public function hasRole($role) : bool
    {
        return isset($this->roles[$role]);
    }

    /**
     * @inheritdoc
     */
    public function setHashedPassword(string $hashedPassword)
    {
        $this->hashedPassword = $hashedPassword;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->id = $id;
    }
} 
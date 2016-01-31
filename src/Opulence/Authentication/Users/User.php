<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Users;

use DateTimeImmutable;

/**
 * Defines a user
 */
class User implements IUser
{
    /** @var int|string The database Id of the user */
    protected $id = -1;
    /** @var string The hashed password of the user */
    protected $hashedPassword = "";
    /** @var DateTimeImmutable The date this user was created */
    protected $dateCreated = null;
    /** @var array The list of roles this user has */
    protected $roles = [];

    /**
     * @param int|string $id The database Id of this user
     * @param string $hashedPassword The hashed password of this user
     * @param DateTimeImmutable $dateCreated The date this user was created
     * @param array $roles The list of roles this user has
     */
    public function __construct($id, string $hashedPassword, DateTimeImmutable $dateCreated, array $roles = [])
    {
        $this->setId($id);
        $this->setHashedPassword($hashedPassword);
        $this->dateCreated = $dateCreated;
        $this->roles = $roles;
    }

    /**
     * @inheritdoc
     */
    public function getDateCreated() : DateTimeImmutable
    {
        return $this->dateCreated;
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
        return $this->roles;
    }

    /**
     * @inheritdoc
     */
    public function hasRole($role) : bool
    {
        return in_array($role, $this->roles);
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
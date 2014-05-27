<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a user
 */
namespace RDev\Models\Users;

class User implements IUser
{
    /** @var int The database Id of the user */
    protected $id = -1;
    /** @var string The username of the user (for now, it'll be the same as the username) */
    protected $username = "";
    /** @var string The email of the user */
    protected $email = "";
    /** @var string The first name of the user */
    protected $firstName = "";
    /** @var string The last name of the user */
    protected $lastName = "";
    /** @var \DateTime The date this user was created */
    protected $dateCreated = null;
    /** @var array The list of roles this user has */
    protected $roles = [];

    /**
     * @param int $id The database Id of this user
     * @param string $username The username of the user
     * @param string $email The email address of this user
     * @param \DateTime $dateCreated The date this user was created
     * @param string $firstName The first name of this user
     * @param string $lastName The last name of this user
     * @param array $roles The list of roles this user has
     */
    public function __construct($id, $username, $email, \DateTime $dateCreated, $firstName, $lastName, array $roles)
    {
        $this->setId($id);
        $this->username = $username;
        $this->setEmail($email);
        $this->dateCreated = $dateCreated;
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->roles = $roles;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Gets whether or not a user has a particular role
     *
     * @param mixed $role The role to search for
     * @return bool True if the user has the role, otherwise false
     */
    public function hasRole($role)
    {
        return in_array($role, $this->roles);
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }
} 
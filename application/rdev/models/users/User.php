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

    /**
     * @param int $id The database Id of this user
     * @param string $username The username of the user
     * @param string $email The email address of this user
     * @param \DateTime $dateCreated The date this user was created
     * @param string $firstName The first name of this user
     * @param string $lastName The last name of this user
     */
    public function __construct($id, $username, $email, \DateTime $dateCreated, $firstName, $lastName)
    {
        $this->setId($id);
        $this->username = $username;
        $this->setEmail($email);
        $this->dateCreated = $dateCreated;
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
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
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
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
<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the base user class
 */
namespace RamODev\API\V1\Users;

require_once(__DIR__ . "/IUser.php");

class User implements IUser
{
    /** @var int The ID of the user */
    protected $id = -1;
    /** @var string The username of the user (for now, it'll be the same as the username) */
    protected $username = "";
    /** @var string The email of the user */
    protected $email = "";
    /** @var string The first name of the user */
    protected $firstName = "";
    /** @var string The last name of the user */
    protected $lastName = "";
    /** @var string The hashed password of the user */
    protected $hashedPassword = null;

    /**
     * @param int $id The ID of this user
     * @param string $username The username of the user
     * @param string $email The email address of this user
     * @param string $firstName The first name of this user
     * @param string $lastName The last name of this user
     * @param string $hashedPassword The hashed password of this user
     */
    public function __construct($id, $username, $hashedPassword, $email, $firstName, $lastName)
    {
        $this->id = $id;
        $this->username = $username;
        $this->hashedPassword = $hashedPassword;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
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
     * @return string
     */
    public function getHashedPassword()
    {
        return $this->hashedPassword;
    }

    /**
     * @return int
     */
    public function getID()
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
     * @param string $password
     */
    public function setHashedPassword($password)
    {
        $this->hashedPassword = $password;
    }

    /**
     * @param int $id
     */
    public function setID($id)
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

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }
} 
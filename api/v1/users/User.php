<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the base user class
 */
namespace RamODev\API\V1\Users;

require_once(__DIR__ . "/IUser.php");

abstract class User implements IUser
{
    /** @var int The ID of the user */
    protected $id = -1;
    /** @var string The email of the user */
    protected $email = "";
    /** @var string The first name of the user */
    protected $firstName = "";
    /** @var string The last name of the user */
    protected $lastName = "";
    /** @var Password The password of the user */
    protected $password = null;

    /**
     * @param int $id The ID of this user
     * @param string $email The email address of this user
     * @param string $firstName The first name of this user
     * @param string $lastName The last name of this user
     * @param Password $password The password object belonging to this user
     */
    public function __construct($id, $email, $firstName, $lastName, Password $password)
    {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->password = $password;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param Password $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return Password
     */
    public function getPassword()
    {
        return $this->password;
    }
} 
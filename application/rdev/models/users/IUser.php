<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the user interface
 */
namespace RDev\Models\Users;
use RDev\Models;

interface IUser extends Models\IEntity
{
    /**
     * @return \DateTime
     */
    public function getDateCreated();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @return string
     */
    public function getLastName();

    /**
     * @return array
     */
    public function getRoles();

    /**
     * @return string
     */
    public function getUsername();

    /**
     * Gets whether or not a user has a particular role
     *
     * @param mixed $role The role to search for
     * @return bool True if the user has the role, otherwise false
     */
    public function hasRole($role);

    /**
     * @param string $email
     */
    public function setEmail($email);

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName);

    /**
     * @param string $lastName
     */
    public function setLastName($lastName);
} 
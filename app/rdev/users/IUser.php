<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the user interface
 */
namespace RDev\Users;
use DateTime;

interface IUser
{
    /**
     * Gets the date the user was created
     *
     * @return DateTime The date the user was created
     */
    public function getDateCreated();

    /**
     * Gets the database Id
     *
     * @return int|string The database Id
     */
    public function getId();

    /**
     * Gets the list of this user's roles
     *
     * @return array
     */
    public function getRoles();

    /**
     * Gets whether or not a user has a particular role
     *
     * @param mixed $role The role to search for
     * @return bool True if the user has the role, otherwise false
     */
    public function hasRole($role);

    /**
     * Sets the database Id of the user
     *
     * @param int|string $id The database Id
     */
    public function setId($id);
} 
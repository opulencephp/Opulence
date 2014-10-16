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
     * Gets the date the user was created
     *
     * @return \DateTime The date the user was created
     */
    public function getDateCreated();

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
} 
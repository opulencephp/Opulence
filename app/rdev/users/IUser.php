<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the user interface
 */
namespace RDev\Users;
use DateTime;
use RDev\ORM\IEntity;

interface IUser extends IEntity
{
    /**
     * Gets the date the user was created
     *
     * @return DateTime The date the user was created
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
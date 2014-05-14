<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the user interface
 */
namespace RamODev\Application\Shared\Models\Users;
use RamODev\Application\Shared\Models;

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
     * @return string
     */
    public function getUsername();

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
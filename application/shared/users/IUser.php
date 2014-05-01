<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the user interface
 */
namespace RamODev\Application\Shared\Users;

interface IUser
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
     * @return int
     */
    public function getId();

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
     * @param int $id
     */
    public function setId($id);

    /**
     * @param string $lastName
     */
    public function setLastName($lastName);
} 
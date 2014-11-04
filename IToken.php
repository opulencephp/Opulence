<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for cryptographic tokens
 */
namespace RDev\Cryptography;
use RDev\ORM;

interface IToken extends ORM\IEntity
{
    /**
     * Marks this token is inactive
     */
    public function deactivate();

    /**
     * Gets the hashed value of this token
     *
     * @return string The hashed value
     */
    public function getHashedValue();

    /**
     * Gets the valid-from date of this token
     *
     * @return \DateTime The valid-from date
     */
    public function getValidFrom();

    /**
     * Gets the valid-to date of this token
     *
     * @return \DateTime The valid-to date
     */
    public function getValidTo();

    /**
     * Gets whether or not this token is active
     *
     * @return bool True if the token is active, otherwise false
     */
    public function isActive();
}
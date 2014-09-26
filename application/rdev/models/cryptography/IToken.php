<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for cryptographic tokens
 */
namespace RDev\Models\Cryptography;
use RDev\Models;

interface IToken extends Models\IEntity
{
    /**
     * Gets the hash of a token, which is suitable for storage
     *
     * @param string $unhashedValue The unhashed token to hash
     * @param int $hashAlgorithm The hash algorithm constant to use in password_hash
     * @param int $cost The cost of the hash to use
     * @param string $pepper The optional pepper to append prior to hashing the value
     * @return string The hashed token
     */
    public static function generateHashedValue($unhashedValue, $hashAlgorithm, $cost, $pepper = "");

    /**
     * Creates a cryptographically-strong random string
     *
     * @param int $length The desired length of the string
     * @return string The random string
     */
    public static function generateRandomString($length);

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

    /**
     * Verifies that an unhashed value matches the hashed value
     *
     * @param string $unhashedValue The unhashed value to verify
     * @param string $pepper The optional pepper to append prior to verifying the value
     * @return bool True if the unhashed value matches the hashed value
     */
    public function verify($unhashedValue, $pepper = "");
}
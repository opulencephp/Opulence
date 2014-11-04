<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a cryptographic hasher
 */
namespace RDev\Cryptography;

interface IHasher
{
    /**
     * Creates a cryptographically-strong random string
     *
     * @param int $length The desired length of the string
     * @return string The random string
     */
    public static function generateRandomString($length);

    /**
     * Verifies that an unhashed value matches the hashed value
     *
     * @param string $hashedValue The hashed value to verify against
     * @param string $unhashedValue The unhashed value to verify
     * @param string $pepper The optional pepper to append prior to verifying the value
     * @return bool True if the unhashed value matches the hashed value
     */
    public static function verify($hashedValue, $unhashedValue, $pepper = "");

    /**
     * Gets the hash of a value, which is suitable for storage
     *
     * @param string $unhashedValue The unhashed value to hash
     * @param array $options The list of algorithm-dependent options
     * @param string $pepper The optional pepper to append prior to hashing the value
     * @return string The hashed value
     * @throws \RuntimeException Thrown if the hashing failed
     */
    public function generate($unhashedValue, array $options = [], $pepper = "");

    /**
     * Checks if a hashed value was hashed with the input options
     *
     * @param string $hashedValue The hashed value to check
     * @param array $options The list of algorithm-specific options
     * @return bool True if the hash needs to be rehashed, otherwise false
     */
    public function needsRehash($hashedValue, array $options = []);
} 
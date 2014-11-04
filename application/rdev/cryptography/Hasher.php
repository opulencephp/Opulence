<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a base cryptographic hasher
 */
namespace RDev\Cryptography;

abstract class Hasher implements IHasher
{
    /** @var int The hash algorithm constant used by this hasher */
    protected $hashAlgorithm = -1;

    public function __construct()
    {
        $this->setHashAlgorithm();
    }

    /**
     * {@inheritdoc}
     */
    public static function generateRandomString($length)
    {
        // N bytes becomes 2N characters in bin2hex(), hence the division by 2
        $string = bin2hex(openssl_random_pseudo_bytes(ceil($length / 2)));

        if($length % 2 == 1)
        {
            // Slice off one character to make it the appropriate odd length
            $string = substr($string, 1);
        }

        return $string;
    }

    /**
     * {@inheritdoc}
     */
    public static function verify($hashedValue, $unhashedValue, $pepper = "")
    {
        return password_verify($unhashedValue . $pepper, $hashedValue);
    }

    /**
     * {@inheritdoc}
     */
    public function generate($unhashedValue, array $options = [], $pepper = "")
    {
        $hashedValue = password_hash($unhashedValue . $pepper, $this->hashAlgorithm, $options);

        if($hashedValue === false)
        {
            throw new \RuntimeException("Failed to generate has for algorithm {$this->hashAlgorithm}");
        }

        return $hashedValue;
    }

    /**
     * {@inheritdoc}
     */
    public function needsRehash($hashedValue, array $options = [])
    {
        return password_needs_rehash($hashedValue, $this->hashAlgorithm, $options);
    }

    /**
     * Should set the hash algorithm property to the algorithm used by the concrete class
     */
    abstract protected function setHashAlgorithm();
} 
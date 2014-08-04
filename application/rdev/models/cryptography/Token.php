<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a cryptographic token used for security
 */
namespace RDev\Models\Cryptography;
use RDev\Models;

class Token implements Models\IEntity
{
    /** @var int The database Id of this token */
    private $id = -1;
    /** @var int The type of token this is */
    private $typeId = -1;
    /** @var int The Id of the user whose token this is */
    private $userId = -1;
    /** @var string The hashed value */
    private $hashedValue = "";
    /** @var \DateTime The valid-from date */
    private $validFrom = null;
    /** @var \DateTime The valid-to date */
    private $validTo = null;
    /** @var bool Whether or not this token is active */
    private $isActive = false;

    /**
     * @param int $id The database Id of this token
     * @param int $typeId The type of token this is
     * @param int $userId The Id of the user whose token this is
     * @param string $hashedValue The hashed value
     * @param \DateTime $validFrom The valid-from date
     * @param \DateTime $validTo The valid-to date
     * @param bool $isActive Whether or not this token is active
     */
    public function __construct($id, $typeId, $userId, $hashedValue, \DateTime $validFrom, \DateTime $validTo, $isActive)
    {
        $this->id = $id;
        $this->typeId = $typeId;
        $this->userId = $userId;
        $this->hashedValue = $hashedValue;
        $this->validFrom = $validFrom;
        $this->validTo = $validTo;
        $this->isActive = $isActive;
    }

    /**
     * Gets the hash of a token, which is suitable for storage
     *
     * @param string $unhashedValue The unhashed token to hash
     * @param int $hashAlgorithm The hash algorithm constant to use in password_hash
     * @param int $cost The cost of the hash to use
     * @param string $pepper The optional pepper to append prior to hashing the value
     * @return string The hashed token
     */
    public static function generateHashedValue($unhashedValue, $hashAlgorithm, $cost, $pepper = "")
    {
        return password_hash($unhashedValue . $pepper, $hashAlgorithm, ["cost" => $cost]);
    }

    /**
     * Creates a cryptographically-strong random string
     *
     * @param int $length The desired length of the string
     * @return string The random string
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
     * Marks this token is inactive
     */
    public function deactivate()
    {
        $this->isActive = false;
    }

    /**
     * @return string
     */
    public function getHashedValue()
    {
        return $this->hashedValue;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return \DateTime
     */
    public function getValidFrom()
    {
        return $this->validFrom;
    }

    /**
     * @return \DateTime
     */
    public function getValidTo()
    {
        return $this->validTo;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        $now = new \DateTime("now", new \DateTimeZone("UTC"));

        return $this->isActive && $this->validFrom <= $now && $now < $this->validTo;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Verifies that an unhashed value matches the hashed value
     *
     * @param string $unhashedValue The unhashed value to verify
     * @param string $pepper The optional pepper to append prior to verifying the value
     * @return bool True if the unhashed value matches the hashed value
     */
    public function verify($unhashedValue, $pepper = "")
    {
        return password_verify($unhashedValue . $pepper, $this->hashedValue);
    }
} 
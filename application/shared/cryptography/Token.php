<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a cryptographic token used for security
 */
namespace RamODev\Application\Shared\Cryptography;

class Token
{
    /** @var int The database Id of this token */
    private $id = -1;
    /** @var \DateTime The valid-from date */
    private $validFrom = null;
    /** @var \DateTime The valid-to date */
    private $validTo = null;
    /** @var bool Whether or not this token is active */
    private $isActive = false;

    /**
     * @param int $id The database Id of this token
     * @param \DateTime $validFrom The valid-from date
     * @param \DateTime $validTo The valid-to date
     * @param bool $isActive Whether or not this token is active
     */
    public function __construct($id, \DateTime $validFrom, \DateTime $validTo, $isActive)
    {
        $this->id = $id;
        $this->validFrom = $validFrom;
        $this->validTo = $validTo;
        $this->isActive = $isActive;
    }

    /**
     * Sets the active flag to false
     */
    public function deactivate()
    {
        $this->isActive = false;
    }

    /**
     * Creates a cryptographically-strong random string
     *
     * @param int $length The desired length of the string
     * @return string The random string
     */
    public function generateRandomString($length)
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
     * Gets the database Id of the token
     *
     * @return int The database Id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the valid-from date
     *
     * @return \DateTime The valid-from date
     */
    public function getValidFrom()
    {
        return $this->validFrom;
    }

    /**
     * Gets the valid-to date
     *
     * @return \DateTime The valid-to date
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
     * Sets the database Id for the token
     *
     * @param int $id The Id of the token
     */
    public function setId($id)
    {
        $this->id = $id;
    }
} 
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

    /**
     * @param int $id The database Id of this token
     * @param \DateTime $validFrom The valid-from date
     * @param \DateTime $validTo The valid-to date
     */
    public function __construct($id, $validFrom, $validTo)
    {
        $this->id = $id;
        $this->validFrom = $validFrom;
        $this->validTo = $validTo;
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
     * Gets whether or not this token is expired
     *
     * @return bool True if the token is expired, otherwise false
     */
    public function isExpired()
    {
        $now = new \DateTime("now", new \DateTimeZone("UTC"));

        return $now < $this->validFrom || $this->validTo <= $now;
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
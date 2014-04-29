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
    /** @var string The cryptographic hash value */
    private $value = "";
    /** @var \DateTime The valid-from date */
    private $validFrom = null;
    /** @var \DateTime The valid-to date */
    private $validTo = null;

    /**
     * @param int $id The database Id of this token
     * @param string $value The cryptographic hash value
     * @param \DateTime $validFrom The valid-from date
     * @param \DateTime $validTo The valid-to date
     */
    public function __construct($id, $value, $validFrom, $validTo)
    {
        $this->id = $id;
        $this->value = $value;
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
     * Gets the cryptographic hash value
     *
     * @return string The value of the token
     */
    public function getValue()
    {
        return $this->value;
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
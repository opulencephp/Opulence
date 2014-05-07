<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a token factory
 */
namespace RamODev\Application\Shared\Cryptography\Factories;
use RamODev\Application\Shared\Cryptography;

class TokenFactory
{
    /**
     * Creates a cryptographic token
     *
     * @param \DateTime $validFrom The valid-from date
     * @param \DateTime $validTo The valid-to date
     * @return Cryptography\Token The token
     */
    public function createToken(\DateTime $validFrom, \DateTime $validTo)
    {
        return new Cryptography\Token(-1, $validFrom, $validTo);
    }
} 
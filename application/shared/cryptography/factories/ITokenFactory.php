<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the token factory interface
 */
namespace RamODev\Application\Shared\Cryptography\Factories;
use RamODev\Application\Shared\Cryptography;

interface ITokenFactory
{
    /**
     * Creates a cryptographic token
     *
     * @param int $length The length of the cryptographic token
     * @param \DateTime $validFrom The valid-from date
     * @param \DateTime $validTo The valid-to date
     * @return Cryptography\Token The token
     */
    public function createToken($length, $validFrom, $validTo);
} 
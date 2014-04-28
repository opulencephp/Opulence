<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a token factory
 */
namespace RamODev\Application\Shared\Users\Authentication\Tokens\Factories;

class TokenFactory implements ITokenFactory
{
    /**
     * Creates a cryptographically-strong token string for use with authentication
     *
     * @param int $length The desired length of the token
     * @return string The token string
     */
    public function createToken($length)
    {
        // N bytes becomes 2N characters in bin2hex(), hence the division by 2
        return bin2hex(openssl_random_pseudo_bytes(floor($length / 2)));
    }
} 
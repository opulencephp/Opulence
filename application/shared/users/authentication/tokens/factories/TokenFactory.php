<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a token factory
 */
namespace RamODev\Application\Shared\Users\Authentication\Tokens\Factories;

class TokenFactory implements ITokenFactory
{
    /** The smallest ASCII value we'll use to generate a token */
    const MIN_ASCII_VALUE = 33;
    /** The largest ASCII value we'll use to generate a token */
    const MAX_ASCII_VALUE = 126;

    /**
     * Creates a cryptographically-strong token string for use with authentication
     *
     * @param int $length The desired length of the token
     * @return string The token string
     */
    public function createToken($length)
    {
        return bin2hex(openssl_random_pseudo_bytes($length));
    }
} 
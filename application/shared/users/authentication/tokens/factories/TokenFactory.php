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
     * Creates a token string for use with authentication
     *
     * @param int $length The desired length of the token
     * @return string
     */
    public function createToken($length)
    {
        $token = "";

        for($charIter = 0;$charIter < $length;$charIter++)
        {
            $token .= chr(rand(self::MIN_ASCII_VALUE, self::MAX_ASCII_VALUE));
        }

        return $token;
    }
} 
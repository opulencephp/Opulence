<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the token factory interface
 */
namespace RamODev\Application\Shared\Users\Authentication\Tokens\Factories;

interface ITokenFactory
{
    /**
     * Creates a cryptographically-strong token string for use with authentication
     *
     * @param int $length The desired length of the token
     * @return string The token string
     */
    public function createToken($length);
} 
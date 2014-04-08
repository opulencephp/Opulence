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
     * Creates a token string for use with authentication
     *
     * @param int $length The desired length of the token
     * @return string
     */
    public function createToken($length);
} 
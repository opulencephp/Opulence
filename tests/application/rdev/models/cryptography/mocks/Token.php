<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the token for use in testing
 */
namespace RDev\Tests\Models\Cryptography\Mocks;
use RDev\Models\Cryptography;

class Token extends Cryptography\Token
{
    /**
     * Creates a new token for use in testing
     *
     * @return Token An instantiated token class
     */
    public static function create()
    {
        return new Token(1, "foo", new \DateTime("now", new \DateTimeZone("UTC")),
            new \DateTime("+1 week", new \DateTimeZone("UTC")), true);
    }
} 
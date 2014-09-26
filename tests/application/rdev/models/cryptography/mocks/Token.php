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
    public function __construct()
    {
        parent::__construct(1, "foo", new \DateTime("now", new \DateTimeZone("UTC")),
            new \DateTime("+1 week", new \DateTimeZone("UTC")), true);
    }
} 
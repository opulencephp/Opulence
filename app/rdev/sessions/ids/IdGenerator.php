<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the session Id generator
 */
namespace RDev\Sessions\Ids;
use RDev\Cryptography\Utilities\Strings;

class IdGenerator implements IIdGenerator
{
    /** The default length of an Id */
    const DEFAULT_LENGTH = 40;

    /** @var Strings The strings utility */
    private $strings = null;

    /**
     * @param Strings $strings The strings utility
     */
    public function __construct(Strings $strings)
    {
        $this->strings = $strings;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($length = self::DEFAULT_LENGTH)
    {
        return $this->strings->generateRandomString($length);
    }
}
<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the session Id generator
 */
namespace Opulence\Sessions\Ids;

use Opulence\Cryptography\Utilities\Strings;

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
     * @inheritdoc
     */
    public function generate($length = self::DEFAULT_LENGTH)
    {
        return $this->strings->generateRandomString($length);
    }

    /**
     * @inheritdoc
     */
    public function isIdValid($id)
    {
        $regex = sprintf(
            "/^[a-z0-9]{%d,%d}$/i",
            self::MIN_LENGTH,
            self::MAX_LENGTH
        );

        return is_string($id) && preg_match($regex, $id);
    }
}
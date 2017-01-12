<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Sessions\Ids\Generators;

/**
 * Defines the session Id generator
 */
class IdGenerator implements IIdGenerator
{
    /** The default length of an Id */
    const DEFAULT_LENGTH = 40;

    /**
     * @inheritdoc
     */
    public function generate($length = self::DEFAULT_LENGTH)
    {
        // N bytes becomes 2N characters in bin2hex(), hence the division by 2
        $string = \bin2hex(\random_bytes(\ceil($length / 2)));

        if ($length % 2 === 1) {
            // Slice off one character to make it the appropriate odd length
            $string = \mb_substr($string, 1);
        }

        return $string;
    }

    /**
     * @inheritdoc
     */
    public function idIsValid($id) : bool
    {
        $regex = \sprintf(
            '/^[a-z0-9]{%d,%d}$/i',
            self::MIN_LENGTH,
            self::MAX_LENGTH
        );

        return \is_string($id) && \preg_match($regex, $id);
    }
}

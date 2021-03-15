<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Collections;

use RuntimeException;
use Throwable;

/**
 * Defines the key hasher
 * @internal
 */
class KeyHasher
{
    /**
     * Gets the hash key for a value
     *
     * @param string|float|int|object|array|resource $value The value whose hash key we want
     * @return string The value's hash key
     * @throws RuntimeException Thrown if the value's hash key could not be calculated
     */
    public function getHashKey($value) : string
    {
        if (is_string($value)) {
            return "__opulence:s:$value";
        }

        if (is_int($value)) {
            return "__opulence:i:$value";
        }

        if (is_float($value)) {
            return "__opulence:f:$value";
        }

        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                return "__opulence:so:$value";
            }

            return '__opulence:o:' . spl_object_hash($value);
        }

        if (is_array($value)) {
            return '__opulence:a:' . md5(serialize($value));
        }

        if (is_resource($value)) {
            return '__opulence:r:' . "$value";
        }

        // As a last-ditch effort, try to convert the value to a string
        try {
            return '__opulence:u' . (string)$value;
        } catch (Throwable $ex) {
            throw new RuntimeException('Value could not be converted to a key', 0, $ex);
        }
    }
}

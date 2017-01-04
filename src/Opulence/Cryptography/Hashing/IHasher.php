<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Cryptography\Hashing;

use RuntimeException;

/**
 * Defines a cryptographic hasher
 */
interface IHasher
{
    /**
     * Verifies that an unhashed value matches the hashed value
     *
     * @param string $hashedValue The hashed value to verify against
     * @param string $unhashedValue The unhashed value to verify
     * @param string $pepper The optional pepper to append prior to verifying the value
     * @return bool True if the unhashed value matches the hashed value
     */
    public static function verify(string $hashedValue, string $unhashedValue, string $pepper = "") : bool;

    /**
     * Gets the hash of a value, which is suitable for storage
     *
     * @param string $unhashedValue The unhashed value to hash
     * @param array $options The list of algorithm-dependent options
     * @param string $pepper The optional pepper to append prior to hashing the value
     * @return string The hashed value
     * @throws RuntimeException Thrown if the hashing failed
     */
    public function hash(string $unhashedValue, array $options = [], string $pepper = "") : string;

    /**
     * Checks if a hashed value was hashed with the input options
     *
     * @param string $hashedValue The hashed value to check
     * @param array $options The list of algorithm-specific options
     * @return bool True if the hash needs to be rehashed, otherwise false
     */
    public function needsRehash(string $hashedValue, array $options = []) : bool;
}

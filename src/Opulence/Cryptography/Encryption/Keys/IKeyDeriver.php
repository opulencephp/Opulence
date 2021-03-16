<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Cryptography\Encryption\Keys;

/**
 * Defines the interface for key derivers to implement
 */
interface IKeyDeriver
{
    /** The number of bytes the key salt should be */
    const KEY_SALT_BYTE_LENGTH = 32;

    /**
     * Derives suitable encryption keys from a cryptographically-strong key
     *
     * @param string $key The cryptographically-strong key
     * @param string $salt The salt to use on the keys
     * @param int $keyByteLength The desired number of bytes the keys should be
     * @return DerivedKeys The derived keys
     */
    public function deriveKeysFromKey(string $key, string $salt, int $keyByteLength) : DerivedKeys;

    /**
     * Derives suitable encryption keys from a password
     *
     * @param string $password The user-supplied encryption password
     * @param string $salt The salt to use on the keys
     * @param int $keyByteLength The desired number of bytes the keys should be
     * @return DerivedKeys The derived keys
     */
    public function deriveKeysFromPassword(string $password, string $salt, int $keyByteLength) : DerivedKeys;
}

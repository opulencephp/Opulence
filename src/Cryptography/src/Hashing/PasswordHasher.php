<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Cryptography\Hashing;

use RuntimeException;

/**
 * Defines a base cryptographic password hasher
 */
class PasswordHasher implements IHasher
{
    /** @var int|string The hash algorithm constant used by this hasher */
    private $hashAlgorithm;
    /** @var array The options to use (same as the ones in password_hash()) */
    private array $options;

    /**
     * @param int|string $hashAlgorithm The hashing algorithm to use
     * @param array $options The options to use (same as the ones in password_hash())
     */
    protected function __construct($hashAlgorithm, array $options = [])
    {
        $this->hashAlgorithm = $hashAlgorithm;
        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function hash(string $unhashedValue, string $pepper = ''): string
    {
        $hashedValue = \password_hash($unhashedValue . $pepper, $this->hashAlgorithm, $this->options);

        if ($hashedValue === false) {
            throw new RuntimeException("Failed to generate hash for algorithm {$this->hashAlgorithm}");
        }

        return $hashedValue;
    }

    /**
     * @inheritdoc
     */
    public function needsRehash(string $hashedValue): bool
    {
        return \password_needs_rehash($hashedValue, $this->hashAlgorithm, $this->options);
    }

    /**
     * @inheritdoc
     */
    public function verify(string $hashedValue, string $unhashedValue, string $pepper = ''): bool
    {
        return \password_verify($unhashedValue . $pepper, $hashedValue);
    }
}

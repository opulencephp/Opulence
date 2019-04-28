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
 * Defines a base cryptographic hasher
 */
abstract class Hasher implements IHasher
{
    /** @var int The hash algorithm constant used by this hasher */
    protected $hashAlgorithm = -1;

    public function __construct()
    {
        $this->setHashAlgorithm();
    }

    /**
     * @inheritdoc
     */
    public function hash(string $unhashedValue, array $options = [], string $pepper = ''): string
    {
        $hashedValue = \password_hash($unhashedValue . $pepper, $this->hashAlgorithm, $options);

        if ($hashedValue === false) {
            throw new RuntimeException("Failed to generate hash for algorithm {$this->hashAlgorithm}");
        }

        return $hashedValue;
    }

    /**
     * @inheritdoc
     */
    public function needsRehash(string $hashedValue, array $options = []): bool
    {
        return \password_needs_rehash($hashedValue, $this->hashAlgorithm, $options);
    }

    /**
     * @inheritdoc
     */
    public function verify(string $hashedValue, string $unhashedValue, string $pepper = ''): bool
    {
        return \password_verify($unhashedValue . $pepper, $hashedValue);
    }

    /**
     * Should set the hash algorithm property to the algorithm used by the concrete class
     */
    abstract protected function setHashAlgorithm(): void;
}

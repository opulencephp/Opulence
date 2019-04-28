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

/**
 * Defines the Bcrypt cryptographic hasher
 */
class BcryptHasher extends Hasher
{
    /** The default cost used by this hasher */
    public const DEFAULT_COST = 10;

    /**
     * @inheritdoc
     */
    public function hash(string $unhashedValue, array $options = [], string $pepper = ''): string
    {
        if (!isset($options['cost'])) {
            $options['cost'] = self::DEFAULT_COST;
        }

        return parent::hash($unhashedValue, $options, $pepper);
    }

    /**
     * @inheritdoc
     */
    public function needsRehash(string $hashedValue, array $options = []): bool
    {
        if (!isset($options['cost'])) {
            $options['cost'] = self::DEFAULT_COST;
        }

        return parent::needsRehash($hashedValue, $options);
    }

    /**
     * @inheritdoc
     */
    protected function setHashAlgorithm(): void
    {
        $this->hashAlgorithm = PASSWORD_BCRYPT;
    }
}

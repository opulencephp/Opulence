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
class BcryptHasher extends PasswordHasher
{
    /** The default cost used by this hasher */
    private const DEFAULT_COST = 10;

    /**
     * @param array $options The options to use (same as the ones in password_hash())
     */
    public function __construct(array $options = [])
    {
        if (!isset($options['cost'])) {
            $options['cost'] = self::DEFAULT_COST;
        }

        parent::__construct(\PASSWORD_BCRYPT, $options);
    }
}

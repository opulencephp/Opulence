<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Cryptography\Encryption\Keys;

/**
 * Defines encryption and authentication keys that are derived from a user-supplied password
 */
class DerivedKeys
{
    /** @var string The encryption key */
    private string $encryptionKey;
    /** @var string The authentication key */
    private string $authenticationKey;

    /**
     * @param string $encryptionKey The encryption key
     * @param string $authenticationKey The authentication key
     */
    public function __construct(string $encryptionKey, string $authenticationKey)
    {
        $this->encryptionKey = $encryptionKey;
        $this->authenticationKey = $authenticationKey;
    }

    /**
     * @return string
     */
    public function getAuthenticationKey(): string
    {
        return $this->authenticationKey;
    }

    /**
     * @return string
     */
    public function getEncryptionKey(): string
    {
        return $this->encryptionKey;
    }
}

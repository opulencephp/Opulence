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
 * Defines encryption and authentication keys that are derived from a user-supplied password
 */
class DerivedKeys
{
    /** @var string The encryption key */
    private $encryptionKey = '';
    /** @var string The authentication key */
    private $authenticationKey = '';

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
    public function getAuthenticationKey() : string
    {
        return $this->authenticationKey;
    }

    /**
     * @return string
     */
    public function getEncryptionKey() : string
    {
        return $this->encryptionKey;
    }
}

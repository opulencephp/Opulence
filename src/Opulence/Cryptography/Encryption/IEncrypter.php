<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Cryptography\Encryption;

use Opulence\Cryptography\Encryption\Keys\Secret;

/**
 * Defines the interface for encrypters to implement
 */
interface IEncrypter
{
    /**
     * Decrypts the data
     *
     * @param string $data The data to decrypt
     * @return string The decrypted data
     * @throws EncryptionException Thrown if there was an error decrypting the data
     */
    public function decrypt(string $data) : string;

    /**
     * Encrypts the data
     *
     * @param string $data The data to encrypt
     * @return string The encrypted data
     * @throws EncryptionException Thrown if there was an error encrypting the data
     */
    public function encrypt(string $data) : string;

    /**
     * Sets the encryption secret that will be used to derive keys
     *
     * @param Secret $secret The secret to use
     */
    public function setSecret(Secret $secret);
}

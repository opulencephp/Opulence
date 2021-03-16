<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Sessions\Handlers;

/**
 * Defines the interface for session encrypters to implement
 */
interface ISessionEncrypter
{
    /**
     * Decrypts the data
     *
     * @param string $data The data to decrypt
     * @return string The decrypted data
     * @throws SessionEncryptionException Thrown if there was an error decrypting the data
     */
    public function decrypt(string $data) : string;

    /**
     * Encrypts the data
     *
     * @param string $data The data to encrypt
     * @return string The encrypted data
     * @throws SessionEncryptionException Thrown if there was an error encrypting the data
     */
    public function encrypt(string $data) : string;
}

<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for session handlers that can encrypt session data
 */
namespace Opulence\Sessions\Handlers;

use Opulence\Cryptography\Encryption\IEncrypter;

interface IEncryptableSessionHandler
{
    /**
     * Sets the encrypter to use
     *
     * @param IEncrypter $encrypter The encrypter to use
     */
    public function setEncrypter(IEncrypter $encrypter);

    /**
     * Sets whether or not to use encryption
     *
     * @param bool $useEncryption Whether or not to use encryption
     */
    public function useEncryption($useEncryption);
}
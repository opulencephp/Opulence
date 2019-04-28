<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Sessions\Handlers;

use Opulence\Cryptography\Encryption\EncryptionException;
use Opulence\Cryptography\Encryption\IEncrypter;

/**
 * Defines a session encrypter that uses Opulence's cryptography library
 */
class SessionEncrypter implements ISessionEncrypter
{
    /** @var IEncrypter The Opulence encrypter */
    private $encrypter;

    /**
     * @param IEncrypter $encrypter The Opulence encrypter
     */
    public function __construct(IEncrypter $encrypter)
    {
        $this->encrypter = $encrypter;
    }

    /**
     * @inheritdoc
     */
    public function decrypt(string $data): string
    {
        try {
            return $this->encrypter->decrypt($data);
        } catch (EncryptionException $ex) {
            throw new SessionEncryptionException('Session decryption failed', 0, $ex);
        }
    }

    /**
     * @inheritdoc
     */
    public function encrypt(string $data): string
    {
        try {
            return $this->encrypter->encrypt($data);
        } catch (EncryptionException $ex) {
            throw new SessionEncryptionException('Session encryption failed', 0, $ex);
        }
    }
}

<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Sessions\Handlers;

/**
 * Defines the interface for session handlers that can encrypt session data
 */
interface IEncryptableSessionHandler
{
    /**
     * Sets the encrypter to use
     *
     * @param ISessionEncrypter $encrypter The encrypter to use
     */
    public function setEncrypter(ISessionEncrypter $encrypter);

    /**
     * Sets whether or not to use encryption
     *
     * @param bool $useEncryption Whether or not to use encryption
     */
    public function useEncryption(bool $useEncryption);
}

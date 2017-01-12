<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Cryptography\Encryption;

/**
 * Defines the various ciphers that can be used in encryption
 */
class Ciphers
{
    /** The AES 128 bit cipher in CBC mode */
    const AES_128_CBC = 'AES-128-CBC';
    /** The AES 192 bit cipher in CBC mode */
    const AES_192_CBC = 'AES-192-CBC';
    /** The AES 256 bit cipher in CBC mode */
    const AES_256_CBC = 'AES-256-CBC';
    /** The AES 128 bit cipher in CTR mode */
    const AES_128_CTR = 'AES-128-CTR';
    /** The AES 192 bit cipher in CTR mode */
    const AES_192_CTR = 'AES-192-CTR';
    /** The AES 256 bit cipher in CTR mode */
    const AES_256_CTR = 'AES-256-CTR';
}

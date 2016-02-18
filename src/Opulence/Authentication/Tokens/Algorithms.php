<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens;

/**
 * Defines the various algorithms that can be used by tokens
 */
class Algorithms
{
    /** The Bcrypt algorithm */
    const BCRYPT = PASSWORD_BCRYPT;
    /** The CRC32 algorithm */
    const CRC32 = "crc32";
    /** The MD5 algorithm */
    const MD5 = "md5";
    /** The SHA1 algorithm */
    const SHA1 = "sha1";
    /** The SHA256 algorithm */
    const SHA256 = "sha256";
    /** The SHA512 algorithm */
    const SHA512 = "sha512";
}
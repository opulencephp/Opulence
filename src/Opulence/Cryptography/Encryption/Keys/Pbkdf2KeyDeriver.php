<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Cryptography\Encryption\Keys;

use InvalidArgumentException;

/**
 * Defines the PBKDF2 key deriver
 */
class Pbkdf2KeyDeriver implements IKeyDeriver
{
    /** The number of bytes a derived key should be */
    const KEY_NUM_BYTES = 32;
    /** The number of iterations to perform when deriving a key */
    const PBKDF2_NUM_ITERATIONS = 10000;
    /** @var int The number of iterations to perform */
    private $numIterations = self::PBKDF2_NUM_ITERATIONS;

    /**
     * @param int $numIterations The number of iterations to perform
     */
    public function __construct(int $numIterations = self::PBKDF2_NUM_ITERATIONS)
    {
        $this->numIterations = $numIterations;
    }

    /**
     * @inheritdoc
     */
    public function deriveKeys(string $password, string $salt) : DerivedKeys
    {
        if (mb_strlen($salt, "8bit") !== self::SALT_NUM_BYTES) {
            throw new InvalidArgumentException("Salt must be " . self::SALT_NUM_BYTES . " bytes long");
        }

        $hash = hash("sha256", $password, true);
        $singleDerivedKey = hash_pbkdf2("sha256", $hash, $salt, $this->numIterations, self::KEY_NUM_BYTES);
        $bothKeys = hash_pbkdf2("sha256", $singleDerivedKey, $salt, 1, self::KEY_NUM_BYTES * 2);
        $authenticationKey = substr($bothKeys, 0, self::KEY_NUM_BYTES);
        $encryptionKey = substr($bothKeys, self::KEY_NUM_BYTES);

        return new DerivedKeys($encryptionKey, $authenticationKey);
    }
}
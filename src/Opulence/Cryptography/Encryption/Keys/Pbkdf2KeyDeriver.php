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
    /** The number of iterations to perform when deriving a key */
    const PBKDF2_NUM_ITERATIONS = 25000;
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
    public function deriveKeysFromKey(string $key, string $salt, int $keyByteLength) : DerivedKeys
    {
        $this->validateSaltLength($salt);
        $bothKeys = hash_pbkdf2("sha512", $key, $salt, 1, $keyByteLength * 2);
        $authenticationKey = substr($bothKeys, 0, $keyByteLength);
        $encryptionKey = substr($bothKeys, $keyByteLength);

        return new DerivedKeys($encryptionKey, $authenticationKey);
    }

    /**
     * @inheritdoc
     */
    public function deriveKeysFromPassword(string $password, string $salt, int $keyByteLength) : DerivedKeys
    {
        $this->validateSaltLength($salt);
        $hash = hash("sha512", $password, true);
        $singleDerivedKey = hash_pbkdf2("sha512", $hash, $salt, $this->numIterations, $keyByteLength);
        $bothKeys = hash_pbkdf2("sha512", $singleDerivedKey, $salt, 1, $keyByteLength * 2);
        $authenticationKey = substr($bothKeys, 0, $keyByteLength);
        $encryptionKey = substr($bothKeys, $keyByteLength);

        return new DerivedKeys($encryptionKey, $authenticationKey);
    }

    /**
     * Verifies the salt length
     *
     * @param string $salt The salt to validate
     * @throws InvalidArgumentException Thrown if the salt is not the correct length
     */
    private function validateSaltLength(string $salt)
    {
        if (mb_strlen($salt, "8bit") !== self::KEY_SALT_NUM_BYTES) {
            throw new InvalidArgumentException("Salt must be " . self::KEY_SALT_NUM_BYTES . " bytes long");
        }
    }
}
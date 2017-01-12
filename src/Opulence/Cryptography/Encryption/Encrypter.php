<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Cryptography\Encryption;

use Exception;
use Opulence\Cryptography\Encryption\Keys\DerivedKeys;
use Opulence\Cryptography\Encryption\Keys\IKeyDeriver;
use Opulence\Cryptography\Encryption\Keys\Pbkdf2KeyDeriver;
use Opulence\Cryptography\Encryption\Keys\Secret;
use Opulence\Cryptography\Encryption\Keys\SecretTypes;

/**
 * Defines an encrypter
 */
class Encrypter implements IEncrypter
{
    /** @var array The list of approved ciphers */
    protected static $approvedCiphers = [
        Ciphers::AES_128_CBC,
        Ciphers::AES_192_CBC,
        Ciphers::AES_256_CBC,
        Ciphers::AES_128_CTR,
        Ciphers::AES_192_CTR,
        Ciphers::AES_256_CTR
    ];
    /** @var string The current version of this encrypter */
    private static $version = '1.0.0';
    /** @var string The HMAC algorithm */
    private static $hmacAlgorithm = 'sha512';
    /** @var string The byte length of generated HMACs */
    private static $hmacByteLength = 128;
    /** @var Secret The encryption secret that will be used to derive keys */
    private $secret = null;
    /** @var string The encryption cipher */
    private $cipher = Ciphers::AES_256_CTR;
    /** @var IKeyDeriver The key deriver to use */
    private $keyDeriver = null;

    /**
     * @param Secret $secret The encryption secret that will be used to derive keys
     * @param string $cipher The encryption cipher
     * @param IKeyDeriver $keyDeriver The key deriver
     */
    public function __construct(Secret $secret, string $cipher = Ciphers::AES_256_CTR, IKeyDeriver $keyDeriver = null)
    {
        $this->setCipher($cipher);
        $this->setSecret($secret);
        $this->keyDeriver = $keyDeriver ?? new Pbkdf2KeyDeriver();
    }

    /**
     * @inheritdoc
     */
    public function decrypt(string $data) : string
    {
        $pieces = $this->getPieces($data);
        $encodedIv = $pieces['iv'];
        $decodedIv = \base64_decode($encodedIv);
        $encodedKeySalt = $pieces['keySalt'];
        $decodedKeySalt = \base64_decode($encodedKeySalt);
        $encryptedValue = $pieces['value'];
        $cipher = $pieces['cipher'];
        $derivedKeys = $this->deriveKeys($cipher, $decodedKeySalt);
        $correctHmac = $this->createHmac(
            $encodedIv,
            $encodedKeySalt,
            $cipher,
            $encryptedValue,
            $derivedKeys->getAuthenticationKey()
        );
        $userHmac = $pieces['hmac'];

        if (!\hash_equals($correctHmac, $userHmac)) {
            throw new EncryptionException('Invalid HMAC');
        }

        try {
            $decryptedData = \openssl_decrypt(
                $encryptedValue,
                $cipher,
                $derivedKeys->getEncryptionKey(),
                0,
                $decodedIv
            );

            if ($decryptedData === false) {
                throw new EncryptionException('Failed to decrypt data');
            }
        } catch (Exception $ex) {
            throw new EncryptionException('Failed to decrypt data', 0, $ex);
        }

        // In case the data was not a primitive, unserialize it
        return \unserialize($decryptedData);
    }

    /**
     * @inheritdoc
     */
    public function encrypt(string $data) : string
    {
        $decodedIv = \random_bytes(\openssl_cipher_iv_length($this->cipher));
        $encodedIv = \base64_encode($decodedIv);
        $decodedKeySalt = \random_bytes(IKeyDeriver::KEY_SALT_BYTE_LENGTH);
        $encodedKeySalt = \base64_encode($decodedKeySalt);
        $derivedKeys = $this->deriveKeys($this->cipher, $decodedKeySalt);
        $encryptedValue = \openssl_encrypt(
            \serialize($data),
            $this->cipher,
            $derivedKeys->getEncryptionKey(),
            0,
            $decodedIv
        );

        if ($encryptedValue === false) {
            throw new EncryptionException('Failed to encrypt the data');
        }

        $hmac = $this->createHmac(
            $encodedIv,
            $encodedKeySalt,
            $this->cipher,
            $encryptedValue,
            $derivedKeys->getAuthenticationKey()
        );
        $pieces = [
            'version' => self::$version,
            'cipher' => $this->cipher,
            'iv' => $encodedIv,
            'keySalt' => $encodedKeySalt,
            'value' => $encryptedValue,
            'hmac' => $hmac
        ];

        return \base64_encode(\json_encode($pieces));
    }

    /**
     * @inheritdoc
     */
    public function setSecret(Secret $secret)
    {
        $this->secret = $secret;
        $this->validateSecret($this->cipher);
    }

    /**
     * Creates an HMAC
     *
     * @param string $iv The initialization vector
     * @param string $keySalt The key salt
     * @param string $cipher The cipher used
     * @param string $value The value to hash
     * @param string $authenticationKey The authentication key
     * @return string The HMAC
     */
    private function createHmac(
        string $iv,
        string $keySalt,
        string $cipher,
        string $value,
        string $authenticationKey
    ) : string {
        return \hash_hmac(self::$hmacAlgorithm, self::$version . $cipher . $iv . $keySalt . $value, $authenticationKey);
    }

    /**
     * Derives keys that are suitable for encryption and decryption
     *
     * @param string $cipher The cipher used
     * @param string $keySalt The salt to use on the keys
     * @return DerivedKeys The derived keys
     */
    private function deriveKeys(string $cipher, string $keySalt) : DerivedKeys
    {
        // Extract the number of bytes from the cipher
        $keyByteLength = $this->getKeyByteLengthForCipher($cipher);

        if ($this->secret->getType() === SecretTypes::KEY) {
            return $this->keyDeriver->deriveKeysFromKey($this->secret->getValue(), $keySalt, $keyByteLength);
        } else {
            return $this->keyDeriver->deriveKeysFromPassword($this->secret->getValue(), $keySalt, $keyByteLength);
        }
    }

    /**
     * Gets the length of the cipher in bytes
     *
     * @param string $cipher The cipher whose bytes we want
     * @return int The number of bytes
     */
    private function getKeyByteLengthForCipher(string $cipher) : int
    {
        return (int)\mb_substr($cipher, 4, 3, '8bit') / 8;
    }

    /**
     * Converts the input data to an array of pieces
     *
     * @param string $data The JSON data to convert
     * @return array The pieces
     * @throws EncryptionException Thrown if the pieces were not correctly set
     */
    private function getPieces(string $data) : array
    {
        $pieces = \json_decode(\base64_decode($data), true);

        if ($pieces === false ||
            !isset($pieces['version'], $pieces['hmac'], $pieces['value'], $pieces['iv'], $pieces['keySalt'], $pieces['cipher'])
        ) {
            throw new EncryptionException('Data is not in correct format');
        }

        if (!in_array($pieces['cipher'], self::$approvedCiphers)) {
            throw new EncryptionException("Cipher \"{$pieces['ciper']}\" is not supported");
        }

        if (\mb_strlen(\base64_decode($pieces['iv']), '8bit') !== \openssl_cipher_iv_length($pieces['cipher'])) {
            throw new EncryptionException('IV is incorrect length');
        }

        if (\mb_strlen(\base64_decode($pieces['keySalt']), '8bit') !== IKeyDeriver::KEY_SALT_BYTE_LENGTH) {
            throw new EncryptionException('Key salt is incorrect length');
        }

        if (\mb_strlen($pieces['hmac'], '8bit') !== self::$hmacByteLength) {
            throw new EncryptionException('HMAC is incorrect length');
        }

        return $pieces;
    }

    /**
     * @inheritdoc
     */
    private function setCipher(string $cipher)
    {
        $cipher = \mb_strtoupper($cipher, '8bit');

        if (!in_array($cipher, self::$approvedCiphers)) {
            throw new EncryptionException("Invalid cipher \"$cipher\"");
        }

        $this->cipher = $cipher;
    }

    /**
     * Validates the secret
     *
     * @param string $cipher The cipher used
     * @throws EncryptionException Thrown if the secret is not valid
     */
    private function validateSecret(string $cipher)
    {
        if ($this->secret->getType() === SecretTypes::KEY) {
            if (\mb_strlen($this->secret->getValue(), '8bit') < $this->getKeyByteLengthForCipher($cipher)) {
                throw new EncryptionException("Key must be at least {$this->getKeyByteLengthForCipher($cipher)} bytes long");
            }
        } elseif (\mb_strlen($this->secret->getValue(), '8bit') === 0) {
            throw new EncryptionException('Password cannot be empty');
        }
    }
}

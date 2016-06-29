<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Cryptography\Encryption;

use Exception;
use Opulence\Cryptography\Encryption\Keys\IKeyDeriver;
use Opulence\Cryptography\Encryption\Keys\Pbkdf2KeyDeriver;

/**
 * Defines an encrypter
 */
class Encrypter implements IEncrypter
{
    /** @var array The list of approved ciphers */
    protected static $approvedCiphers = [
        "AES-128-CBC",
        "AES-192-CBC",
        "AES-256-CBC",
        "AES-128-CTR",
        "AES-192-CTR",
        "AES-256-CTR"
    ];
    /** @var string The encryption password that will be used to derive keys */
    private $password = "";
    /** @var string The encryption cipher */
    private $cipher = "AES-256-CTR";
    /** @var IKeyDeriver The key deriver to use */
    private $keyDeriver = null;

    /**
     * @param string $password The encryption password that will be used to derive keys
     * @param string $cipher The encryption cipher
     * @param IKeyDeriver $keyDeriver The key deriver
     */
    public function __construct(string $password, string $cipher = "AES-256-CTR", IKeyDeriver $keyDeriver = null)
    {
        $this->setPassword($password);
        $this->setCipher($cipher);
        $this->keyDeriver = $keyDeriver ?? new Pbkdf2KeyDeriver();
    }

    /**
     * @inheritdoc
     */
    public function decrypt(string $data) : string
    {
        $pieces = $this->getPieces($data);
        $encodedIv = $pieces["iv"];
        $keySalt = base64_decode($pieces["keySalt"]);
        $encryptedValue = $pieces["value"];
        $derivedKeys = $this->keyDeriver->deriveKeys($this->password, $keySalt);
        $correctHmac = $this->createHmac($encodedIv, $encryptedValue, $derivedKeys->getAuthenticationKey());
        $userHmac = $pieces["hmac"];

        if (!$this->hmacIsValid($correctHmac, $userHmac)) {
            throw new EncryptionException("Invalid MAC");
        }

        $decodedIv = base64_decode($encodedIv);

        try {
            $decryptedData = openssl_decrypt(
                $encryptedValue,
                $this->cipher,
                $derivedKeys->getEncryptionKey(),
                0,
                $decodedIv
            );

            if ($decryptedData === false) {
                throw new EncryptionException("Failed to decrypt data");
            }
        } catch (Exception $ex) {
            throw new EncryptionException("Failed to decrypt data", 0, $ex);
        }

        // In case the data was not a primitive, unserialize it
        return unserialize($decryptedData);
    }

    /**
     * @inheritdoc
     */
    public function encrypt(string $data) : string
    {
        $decodedIv = random_bytes(openssl_cipher_iv_length($this->cipher));
        $keySalt = random_bytes(IKeyDeriver::SALT_NUM_BYTES);
        $derivedKeys = $this->keyDeriver->deriveKeys($this->password, $keySalt);
        $encryptedValue = openssl_encrypt(
            serialize($data),
            $this->cipher,
            $derivedKeys->getEncryptionKey(),
            0,
            $decodedIv
        );

        if ($encryptedValue === false) {
            throw new EncryptionException("Failed to encrypt the data");
        }

        $encodedIv = base64_encode($decodedIv);
        $keySalt = base64_encode($keySalt);
        $hmac = $this->createHmac($encodedIv, $encryptedValue, $derivedKeys->getAuthenticationKey());
        $pieces = [
            "iv" => $encodedIv,
            "keySalt" => $keySalt,
            "value" => $encryptedValue,
            "hmac" => $hmac
        ];

        return base64_encode(json_encode($pieces));
    }

    /**
     * @inheritdoc
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * Creates an HMAC
     *
     * @param string $iv The initialization vector
     * @param string $value The value to hash
     * @param string $authenticationKey The authentication key
     * @return string The HMAC
     */
    private function createHmac(string $iv, string $value, string $authenticationKey) : string
    {
        return hash_hmac("sha256", $iv . $value, $authenticationKey);
    }

    /**
     * Converts the input data to an array of pieces
     *
     * @param string $data The JSON data to convert
     * @return array The pieces
     * @throws EncryptionException Thrown if the data was not valid JSON
     */
    private function getPieces(string $data) : array
    {
        $pieces = json_decode(base64_decode($data), true);

        if ($pieces === false || !isset($pieces["hmac"]) || !isset($pieces["value"]) || !isset($pieces["iv"])
            || !isset($pieces["keySalt"])
        ) {
            throw new EncryptionException("Data is not in correct format");
        }

        return $pieces;
    }

    /**
     * Gets whether or not a HMAC is valid
     *
     * @param string $correctHmac The correct HMAC
     * @param string $userHmac The user-supplied HMAC to check
     * @return bool True if the HMAC is valid, otherwise false
     */
    private function hmacIsValid(string $correctHmac, string $userHmac) : bool
    {
        return hash_equals(
            $correctHmac,
            $userHmac
        );
    }

    /**
     * @inheritdoc
     */
    private function setCipher(string $cipher)
    {
        $cipher = strtoupper($cipher);

        if (!in_array($cipher, self::$approvedCiphers)) {
            throw new EncryptionException("Invalid cipher \"$cipher\"");
        }

        $this->cipher = $cipher;
    }
}
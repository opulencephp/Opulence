<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines an encrypter
 */
namespace RDev\Cryptography\Encryption;
use Exception;
use RDev\Cryptography\Utilities\Strings;

class Encrypter implements IEncrypter
{
    /** @var string The encryption key */
    private $key = "";
    /** @var Strings The string utility */
    private $strings = null;
    /** @var string The encryption cipher */
    private $cipher = MCRYPT_RIJNDAEL_128;
    /** @var string The encryption mode */
    private $mode = MCRYPT_MODE_CBC;
    /** @var int The block size */
    private $blockSize = 16;

    /**
     * @param string $key The encryption key
     * @param Strings $strings The string utility
     * @param string $cipher The encryption cipher
     * @param string $mode The encryption mode
     */
    public function __construct($key, Strings $strings, $cipher = MCRYPT_RIJNDAEL_128, $mode = MCRYPT_MODE_CBC)
    {
        $this->setKey($key);
        $this->strings = $strings;
        $this->setCipher($cipher);
        $this->setMode($mode);
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt($data)
    {
        $pieces = $this->getPieces($data);

        if(!$this->isValidMAC($pieces))
        {
            throw new EncryptionException("Invalid MAC");
        }

        $pieces = array_map("base64_decode", $pieces);

        try
        {
            $decryptedData = mcrypt_decrypt($this->cipher, $this->key, $pieces["value"], $this->mode, $pieces["iv"]);

            if($decryptedData === false)
            {
                throw new EncryptionException("Failed to decrypt data");
            }
        }
        catch(Exception $ex)
        {
            throw new EncryptionException($ex->getMessage());
        }

        // In case the data was not a primitive, unserialize it
        return unserialize($this->removePadding($decryptedData));
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt($data)
    {
        // Figure out the source for the initialization vector
        if(defined("MCRYPT_DEV_URANDOM"))
        {
            $source = MCRYPT_DEV_URANDOM;
        }
        elseif(defined("MCRYPT_DEV_RANDOM"))
        {
            $source = MCRYPT_DEV_RANDOM;
        }
        else
        {
            // Seed the random number generator
            mt_srand();
            $source = MCRYPT_RAND;
        }

        $data = serialize($data);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size($this->cipher, $this->mode), $source);
        $encryptedValue = base64_encode(mcrypt_encrypt($this->cipher, $this->key, $this->pad($data), $this->mode, $iv));
        $iv = base64_encode($iv);
        $mac = $this->createHash($iv, $encryptedValue);
        $pieces = [
            "iv" => $iv,
            "value" => $encryptedValue,
            "mac" => $mac
        ];

        return base64_encode(json_encode($pieces));
    }

    /**
     * {@inheritdoc}
     */
    public function setCipher($cipher)
    {
        $this->cipher = $cipher;
        $this->setBlockSize();
    }

    /**
     * {@inheritdoc}
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        $this->setBlockSize();
    }

    /**
     * Creates a hash
     *
     * @param string $iv The initialization vector
     * @param string $value The value to hash
     * @return string The hash string
     */
    private function createHash($iv, $value)
    {
        return hash_hmac("sha256", $iv . $value, $this->key);
    }

    /**
     * Converts the input data to an array of pieces
     *
     * @param string $data The JSON data to convert
     * @return array The pieces
     * @throws EncryptionException Thrown if the data was not valid JSON
     */
    private function getPieces($data)
    {
        $pieces = json_decode(base64_decode($data), true);

        if($pieces === false || !isset($pieces["mac"]) || !isset($pieces["value"]) || !isset($pieces["iv"]))
        {
            throw new EncryptionException("Data is not in correct format");
        }

        return $pieces;
    }

    /**
     * Gets whether or not a MAC is valid
     *
     * @param array $pieces The pieces to validate
     * @return bool True if the HMAC is valid, otherwise false
     */
    private function isValidMAC(array $pieces)
    {
        $randomBytes = $this->strings->generateRandomString(16);
        $correctHMAC = hash_hmac("sha256", $pieces["mac"], $randomBytes, true);
        $generatedHMAC = hash_hmac("sha256", $this->createHash($pieces["iv"], $pieces["value"]), $randomBytes, true);

        return $this->strings->isEqual($correctHMAC, $generatedHMAC);
    }

    /**
     * Adds PKCS7 padding
     *
     * @param string $data The data to pad
     * @return string The padded string
     * @link http://stackoverflow.com/questions/7314901/how-to-add-remove-pkcs7-padding-from-an-aes-encrypted-string
     */
    private function pad($data)
    {
        $pad = $this->blockSize - (strlen($data) % $this->blockSize);

        return $data . str_repeat(chr($pad), $pad);
    }

    /**
     * Removes padding from a value
     *
     * @param string $data The value to remove padding from
     * @return string The value without the padding
     * @link http://stackoverflow.com/questions/7314901/how-to-add-remove-pkcs7-padding-from-an-aes-encrypted-string
     */
    private function removePadding($data)
    {
        $length = strlen($data);
        $pad = ord($data[$length - 1]);

        return substr($data, 0, $length - $pad);
    }

    /**
     * Sets the block size for the cipher and mode
     */
    private function setBlockSize()
    {
        $this->blockSize = mcrypt_get_iv_size($this->cipher, $this->mode);
    }
}
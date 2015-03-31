<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the encrypter
 */
namespace RDev\Cryptography\Encryption;
use RDev\Cryptography\Utilities\Strings;

class EncrypterTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Encrypter The encrypter to use in tests */
    private $encrypter = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->encrypter = new Encrypter("abcdefghijklmnoq", new Strings());
    }

    /**
     * Tests decrypting data without an IV
     */
    public function testDecryptingDataWithoutIV()
    {
        $this->setExpectedException("RDev\\Cryptography\\Encryption\\EncryptionException");
        $data = ["mac" => "foo", "value" => "bar"];
        $this->encrypter->decrypt(json_encode($data));
    }

    /**
     * Tests decrypting data without an HMAC
     */
    public function testDecryptingDataWithoutMAC()
    {
        $this->setExpectedException("RDev\\Cryptography\\Encryption\\EncryptionException");
        $data = ["iv" => "foo", "value" => "bar"];
        $this->encrypter->decrypt(json_encode($data));
    }

    /**
     * Tests decrypting data without a value
     */
    public function testDecryptingDataWithoutValue()
    {
        $this->setExpectedException("RDev\\Cryptography\\Encryption\\EncryptionException");
        $data = ["mac" => "foo", "iv" => "bar"];
        $this->encrypter->decrypt(json_encode($data));
    }

    /**
     * Tests encrypting and decrypting a value
     */
    public function testEncryptingAndDecryptingValue()
    {
        $decryptedValue = "foobar";
        $encryptedValue = $this->encrypter->encrypt($decryptedValue);
        $this->assertNotEquals($decryptedValue, $encryptedValue);
        $this->assertEquals($decryptedValue, $this->encrypter->decrypt($encryptedValue));
    }

    /**
     * Tests encrypting and decrypting a value using a custom cipher
     */
    public function testEncryptingAndDecryptingValueUsingCustomCipher()
    {
        $this->encrypter->setCipher(MCRYPT_RIJNDAEL_256);
        $decryptedValue = "foobar";
        $encryptedValue = $this->encrypter->encrypt($decryptedValue);
        $this->assertNotEquals($decryptedValue, $encryptedValue);
        $this->assertEquals($decryptedValue, $this->encrypter->decrypt($encryptedValue));
    }
}
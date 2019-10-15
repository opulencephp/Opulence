<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Cryptography\Tests\Encryption;

use Opulence\Cryptography\Encryption\Ciphers;
use Opulence\Cryptography\Encryption\Encrypter;
use Opulence\Cryptography\Encryption\EncryptionException;
use Opulence\Cryptography\Encryption\Keys\Key;
use Opulence\Cryptography\Encryption\Keys\Password;
use PHPUnit\Framework\TestCase;

/**
 * Tests the encrypter
 */
class EncrypterTest extends TestCase
{
    private Encrypter $encrypterWithPassword;
    private Encrypter $encrypterWithKey;

    protected function setUp(): void
    {
        $this->encrypterWithPassword = new Encrypter(new Password('abcdefghijklmnoq'));
        $this->encrypterWithKey = new Encrypter(new Key(str_repeat('a', 32)));
    }

    public function testDecryptingDataWithoutCipher(): void
    {
        $this->expectException(EncryptionException::class);
        $data = [
            'version' => '1',
            'iv' => 'bar',
            'keySalt' => 'baz',
            'value' => 'blah',
            'hmac' => 'foo'
        ];
        $this->encrypterWithPassword->decrypt(base64_encode(json_encode($data)));
    }

    public function testDecryptingDataWithoutHmac(): void
    {
        $this->expectException(EncryptionException::class);
        $data = [
            'version' => '1',
            'cipher' => Ciphers::AES_256_CTR,
            'iv' => 'foo',
            'keySalt' => 'bar',
            'value' => 'baz'
        ];
        $this->encrypterWithPassword->decrypt(base64_encode(json_encode($data)));
    }

    public function testDecryptingDataWithoutIV(): void
    {
        $this->expectException(EncryptionException::class);
        $data = [
            'version' => '1',
            'cipher' => Ciphers::AES_256_CTR,
            'keySalt' => 'bar',
            'value' => 'baz',
            'hmac' => 'foo'
        ];
        $this->encrypterWithPassword->decrypt(base64_encode(json_encode($data)));
    }

    public function testDecryptingDataWithoutKeySalt(): void
    {
        $this->expectException(EncryptionException::class);
        $data = [
            'version' => '1',
            'cipher' => Ciphers::AES_256_CTR,
            'iv' => 'bar',
            'value' => 'baz',
            'hmac' => 'foo'
        ];
        $this->encrypterWithPassword->decrypt(base64_encode(json_encode($data)));
    }

    public function testDecryptingDataWithoutValue(): void
    {
        $this->expectException(EncryptionException::class);
        $data = [
            'version' => '1',
            'cipher' => Ciphers::AES_256_CTR,
            'iv' => 'bar',
            'keySalt' => 'baz',
            'hmac' => 'foo'
        ];
        $this->encrypterWithPassword->decrypt(base64_encode(json_encode($data)));
    }

    public function testDecryptingDataWithoutVersion(): void
    {
        $this->expectException(EncryptionException::class);
        $data = [
            'cipher' => Ciphers::AES_256_CTR,
            'iv' => 'bar',
            'keySalt' => 'baz',
            'value' => 'blah',
            'hmac' => 'foo'
        ];
        $this->encrypterWithPassword->decrypt(base64_encode(json_encode($data)));
    }

    public function testDecryptingInvalidJson(): void
    {
        $this->expectException(EncryptionException::class);
        $this->encrypterWithPassword->decrypt('foo');
    }

    public function testDecryptingWithCipherThatIsDifferentFromEncrypters(): void
    {
        $key = str_repeat('a', 16);
        $encrypter1 = new Encrypter(new Key($key), Ciphers::AES_128_CBC);
        $encryptedValue = $encrypter1->encrypt('foo');
        $encrypter2 = new Encrypter(new Key($key), Ciphers::AES_128_CTR);
        $this->assertEquals('foo', $encrypter2->decrypt($encryptedValue));
    }

    public function testEmptyPasswordThrowsException(): void
    {
        $this->expectException(EncryptionException::class);
        new Encrypter(new Password(''));
    }

    public function testEncryptingAndDecryptingValue(): void
    {
        $decryptedValue = 'foobar';
        $encryptedValueWithPassword = $this->encrypterWithPassword->encrypt($decryptedValue);
        $encryptedValueWithKey = $this->encrypterWithKey->encrypt($decryptedValue);
        $this->assertNotEquals($decryptedValue, $encryptedValueWithPassword);
        $this->assertNotEquals($decryptedValue, $encryptedValueWithKey);
        $this->assertNotEquals($encryptedValueWithPassword, $encryptedValueWithKey);
        $this->assertEquals($decryptedValue, $this->encrypterWithPassword->decrypt($encryptedValueWithPassword));
        $this->assertEquals($decryptedValue, $this->encrypterWithKey->decrypt($encryptedValueWithKey));
    }

    public function testIncorrectLengthKeyThrowsException(): void
    {
        $this->expectException(EncryptionException::class);
        new Encrypter(new Key(str_repeat('a', 16)), Ciphers::AES_256_CTR);
    }

    public function testPassingCustomCipherThroughConstructor(): void
    {
        $approvedCiphersToKeyByteLengths = [
            Ciphers::AES_128_CBC => 16,
            Ciphers::AES_192_CBC => 24,
            Ciphers::AES_256_CBC => 32,
            Ciphers::AES_128_CTR => 16,
            Ciphers::AES_192_CTR => 24,
            Ciphers::AES_256_CTR => 32
        ];

        // Test using passwords
        foreach ($approvedCiphersToKeyByteLengths as $cipher => $keyByteLength) {
            $encrypter = new Encrypter(new Password('abcdefghijklmnopq'), $cipher);
            $decryptedValue = 'foobar';
            $encryptedValue = $encrypter->encrypt($decryptedValue);
            $this->assertNotEquals($decryptedValue, $encryptedValue);
            $this->assertEquals($decryptedValue, $encrypter->decrypt($encryptedValue));
        }

        // Test using keys
        foreach ($approvedCiphersToKeyByteLengths as $cipher => $keyByteLength) {
            $encrypter = new Encrypter(new Key(str_repeat('a', $keyByteLength)), $cipher);
            $decryptedValue = 'foobar';
            $encryptedValue = $encrypter->encrypt($decryptedValue);
            $this->assertNotEquals($decryptedValue, $encryptedValue);
            $this->assertEquals($decryptedValue, $encrypter->decrypt($encryptedValue));
        }
    }

    public function testSettingInvalidCipher(): void
    {
        $this->expectException(EncryptionException::class);
        new Encrypter(new Password('foo'), 'bar');
    }
}

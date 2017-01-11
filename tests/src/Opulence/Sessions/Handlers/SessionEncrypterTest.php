<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Sessions\Handlers;

use Opulence\Cryptography\Encryption\EncryptionException;
use Opulence\Cryptography\Encryption\IEncrypter;

/**
 * Tests the session encrypter
 */
class SessionEncrypterTest extends \PHPUnit\Framework\TestCase
{
    /** @var SessionEncrypter The session encrypter to use in tests */
    private $sessionEncrypter = null;
    /** @var IEncrypter|\PHPUnit_Framework_MockObject_MockObject The Opulence encrypter the session encrypter uses */
    private $opulenceEncrypter = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->opulenceEncrypter = $this->createMock(IEncrypter::class);
        $this->sessionEncrypter = new SessionEncrypter($this->opulenceEncrypter);
    }

    /**
     * Tests that the Opulence encrypter's exceptions are converted when decrypting
     */
    public function testOpulenceEncrypterExceptionIsConvertedWhenDecrypting()
    {
        $this->expectException(SessionEncryptionException::class);
        $this->opulenceEncrypter->expects($this->once())
            ->method('decrypt')
            ->with('foo')
            ->willThrowException(new EncryptionException('bar'));
        $this->sessionEncrypter->decrypt('foo');
    }

    /**
     * Tests that the Opulence encrypter's exceptions are converted when encrypting
     */
    public function testOpulenceEncrypterExceptionIsConvertedWhenEncrypting()
    {
        $this->expectException(SessionEncryptionException::class);
        $this->opulenceEncrypter->expects($this->once())
            ->method('encrypt')
            ->with('foo')
            ->willThrowException(new EncryptionException('bar'));
        $this->sessionEncrypter->encrypt('foo');
    }

    /**
     * Tests that the Opulence encrypter is used to decrypt data
     */
    public function testOpulenceEncrypterUsedToDecryptData()
    {
        $this->opulenceEncrypter->expects($this->once())
            ->method('decrypt')
            ->with('foo')
            ->willReturn('bar');
        $this->assertEquals('bar', $this->sessionEncrypter->decrypt('foo'));
    }

    /**
     * Tests that the Opulence encrypter is used to encrypt data
     */
    public function testOpulenceEncrypterUsedToEncryptData()
    {
        $this->opulenceEncrypter->expects($this->once())
            ->method('encrypt')
            ->with('foo')
            ->willReturn('bar');
        $this->assertEquals('bar', $this->sessionEncrypter->encrypt('foo'));
    }
}

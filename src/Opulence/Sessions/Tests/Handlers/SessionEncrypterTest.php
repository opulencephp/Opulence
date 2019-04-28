<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Sessions\Tests\Handlers;

use Opulence\Cryptography\Encryption\EncryptionException;
use Opulence\Cryptography\Encryption\IEncrypter;
use Opulence\Sessions\Handlers\SessionEncrypter;
use Opulence\Sessions\Handlers\SessionEncryptionException;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the session encrypter
 */
class SessionEncrypterTest extends \PHPUnit\Framework\TestCase
{
    /** @var SessionEncrypter The session encrypter to use in tests */
    private $sessionEncrypter;
    /** @var IEncrypter|MockObject The Opulence encrypter the session encrypter uses */
    private $opulenceEncrypter;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->opulenceEncrypter = $this->createMock(IEncrypter::class);
        $this->sessionEncrypter = new SessionEncrypter($this->opulenceEncrypter);
    }

    /**
     * Tests that the Opulence encrypter's exceptions are converted when decrypting
     */
    public function testOpulenceEncrypterExceptionIsConvertedWhenDecrypting(): void
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
    public function testOpulenceEncrypterExceptionIsConvertedWhenEncrypting(): void
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
    public function testOpulenceEncrypterUsedToDecryptData(): void
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
    public function testOpulenceEncrypterUsedToEncryptData(): void
    {
        $this->opulenceEncrypter->expects($this->once())
            ->method('encrypt')
            ->with('foo')
            ->willReturn('bar');
        $this->assertEquals('bar', $this->sessionEncrypter->encrypt('foo'));
    }
}

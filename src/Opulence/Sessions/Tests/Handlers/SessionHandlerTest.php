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

use LogicException;
use Opulence\Sessions\Handlers\ISessionEncrypter;
use Opulence\Sessions\Handlers\SessionEncryptionException;
use Opulence\Sessions\Handlers\SessionHandler;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the base session handler
 */
class SessionHandlerTest extends \PHPUnit\Framework\TestCase
{
    /** @var SessionHandler|MockObject The session handler to use in tests */
    private $handler;
    /** @var ISessionEncrypter|MockObject The encrypter to use in tests */
    private $encrypter;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->handler = $this->getMockForAbstractClass(SessionHandler::class);
        $this->encrypter = $this->createMock(ISessionEncrypter::class);
    }

    /**
     * Tests that an empty string is written when the encrypter fails
     */
    public function testEmptyStringIsWrittenWhenEncrypterFails(): void
    {
        $this->handler->useEncryption(true);
        $this->handler->setEncrypter($this->encrypter);
        $this->handler->expects($this->any())->method('doWrite')->with('theId', '');
        $this->encrypter->expects($this->any())->method('encrypt')
            ->willThrowException(new SessionEncryptionException());
        $this->handler->write('theId', 'foo');
        // Essentially just test that we got here
        $this->assertTrue(true);
    }

    /**
     * Tests that an exception is thrown when reading and the encrypter is not set
     */
    public function testExceptionIsThrownWhenReadingWithEncrypterNotSet(): void
    {
        $this->expectException(LogicException::class);
        $this->handler->useEncryption(true);
        $this->handler->expects($this->any())->method('doRead')->will($this->returnValue('foo'));
        $this->handler->read('baz');
    }

    /**
     * Tests that an exception is thrown when writing and the encrypter is not set
     */
    public function testExceptionIsThrownWhenWritingWithEncrypterNotSet(): void
    {
        $this->expectException(LogicException::class);
        $this->handler->useEncryption(true);
        $this->handler->expects($this->any())->method('doRead')->will($this->returnValue('foo'));
        $this->handler->read('baz');
    }

    /**
     * Tests that data being read is not decrypted when not using an encrypter
     */
    public function testReadDataIsNotDecryptedWhenNotUsingEncrypter(): void
    {
        $this->handler->expects($this->any())->method('doRead')->will($this->returnValue('bar'));
        $this->assertEquals('bar', $this->handler->read('foo'));
    }

    /**
     * Tests reading encrypted data
     */
    public function testReadingEncryptedData(): void
    {
        $this->handler->useEncryption(true);
        $this->handler->setEncrypter($this->encrypter);
        $this->handler->expects($this->any())->method('doRead')->will($this->returnValue('foo'));
        $this->encrypter->expects($this->any())->method('decrypt')->will($this->returnValue('bar'));
        $this->assertEquals('bar', $this->handler->read('baz'));
    }

    /**
     * Tests that a serialized empty array is returned when the encrypter fails to read the data
     */
    public function testSerializedEmptyArrayReturnedWhenEncrypterFailsToReadData(): void
    {
        $this->handler->useEncryption(true);
        $this->handler->setEncrypter($this->encrypter);
        $this->handler->expects($this->any())->method('doRead')->will($this->returnValue('foo'));
        $this->encrypter->expects($this->any())->method('decrypt')
            ->will($this->throwException(new SessionEncryptionException()));
        $this->assertEquals(serialize([]), $this->handler->read('bar'));
    }

    /**
     * Tests writing encrypted data
     */
    public function testWritingEncryptedData(): void
    {
        $this->handler->useEncryption(true);
        $this->handler->setEncrypter($this->encrypter);
        $this->handler->expects($this->any())->method('doWrite')->with('theId', 'bar');
        $this->encrypter->expects($this->any())->method('encrypt')->will($this->returnValue('bar'));
        $this->handler->write('theId', 'foo');
        // Essentially just test that we got here
        $this->assertTrue(true);
    }

    /**
     * Tests that data being written is not encrypted when not using an encrypter
     */
    public function testWrittenDataIsNotEncryptedWhenNotUsingEncrypter(): void
    {
        $this->handler->expects($this->any())->method('doWrite')->with('theId', 'foo');
        $this->handler->write('theId', 'foo');
        // Essentially just test that we got here
        $this->assertTrue(true);
    }
}

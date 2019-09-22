<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\IO\Tests\Streams;

use InvalidArgumentException;
use Opulence\IO\Streams\Stream;
use RuntimeException;

class StreamTest extends \PHPUnit\Framework\TestCase
{
    /** A temporary file to use for non read/write stream tests */
    private const TEMP_FILE = __DIR__ . '/temp.txt';

    protected function tearDown(): void
    {
        if (file_exists(self::TEMP_FILE)) {
            @unlink(self::TEMP_FILE);
        }
    }

    public function testCastingToStringOnClosedStreamReturnsEmptyString(): void
    {
        $handle = fopen('php://temp', 'w+b');
        $stream = new Stream($handle);
        $stream->write('foo');
        $stream->close();
        $this->assertEquals('', (string)$stream);
    }

    public function testCastingToStringRewindsAndReadsToEnd(): void
    {
        $handle = fopen('php://temp', 'w+b');
        $stream = new Stream($handle);
        $stream->write('foo');
        $stream->read(1);
        $this->assertEquals('foo', (string)$stream);
    }

    public function testClosingStreamUnsetsResource(): void
    {
        $handle = fopen('php://temp', 'rb');
        $stream = new Stream($handle);
        $stream->close();
        $this->assertFalse(is_resource($handle));
    }

    public function testCopyingToClosedStreamThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $sourceStream = new Stream(fopen('php://temp', 'r+b'));
        $sourceStream->write('foo');
        $sourceStream->rewind();
        $destinationStream = new Stream(fopen('php://temp', 'r+b'));
        $destinationStream->close();
        $sourceStream->copyToStream($destinationStream, 1);
    }

    public function testCopyingToStreamCopiesAllContentsUsingBufferSize(): void
    {
        $sourceStream = new Stream(fopen('php://temp', 'r+b'));
        $sourceStream->write('foo');
        $sourceStream->rewind();
        $destinationStream = new Stream(fopen('php://temp', 'r+b'));
        $sourceStream->copyToStream($destinationStream, 1);
        $destinationStream->rewind();
        $this->assertEquals('foo', $destinationStream->readToEnd());
    }

    public function testDestructorUnsetsResource(): void
    {
        $handle = fopen('php://temp', 'rb');
        $stream = new Stream($handle);
        unset($stream);
        $this->assertFalse(is_resource($handle));
    }

    public function testGettingLengthOfClosedStreamThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $handle = fopen('php://temp', 'rb');
        $stream = new Stream($handle, 724);
        $stream->close();
        $stream->getLength();
    }

    public function testGettingPositionThrowsExceptionIfStreamIsClosed(): void
    {
        $this->expectException(RuntimeException::class);
        $handle = fopen('php://temp', 'rb');
        $stream = new Stream($handle);
        $stream->close();
        $stream->getPosition();
    }

    public function testIsEofReturnsFalseForStreamsThatAreNotAtEof(): void
    {
        $handle = fopen('php://temp', 'w+b');
        $stream = new Stream($handle);
        $stream->write('foo');
        $this->assertFalse($stream->isEof());
    }

    public function testIsEofThrowsExceptionForClosedStream(): void
    {
        $this->expectException(RuntimeException::class);
        $handle = fopen('php://temp', 'w+b');
        $stream = new Stream($handle);
        $stream->write('foo');
        $stream->close();
        $stream->isEof();
    }

    public function testIsEofReturnsTrueForStreamsAtEof(): void
    {
        $handle = fopen('php://temp', 'w+b');
        $stream = new Stream($handle);
        $stream->write('foo');
        $stream->readToEnd();
        $this->assertTrue($stream->isEof());
    }

    public function testIsReadableReturnsCorrectValueBasedOnItsMode(): void
    {
        $readableHandle = fopen('php://temp', 'rb');
        $readableStream = new Stream($readableHandle);
        $this->assertTrue($readableStream->isReadable());
        $unreadableHandle = fopen(self::TEMP_FILE, 'wb');
        $unreadableStream = new Stream($unreadableHandle);
        $this->assertFalse($unreadableStream->isReadable());
    }

    public function testIsSeekableReturnsCorrectValueBasedOnItsMode(): void
    {
        $seekableHandle = fopen('php://temp', 'r+b');
        $seekableStream = new Stream($seekableHandle);
        $this->assertTrue($seekableStream->isSeekable());
        // Testing unseekable streams is not possible
    }

    public function testIsWritableableReturnsCorrectValueBasedOnItsMode(): void
    {
        $writableHandle = fopen('php://temp', 'wb');
        $writableStream = new Stream($writableHandle);
        $this->assertTrue($writableStream->isWritable());
        $unwritableHandle = fopen('php://temp', 'rb');
        $unwritableStream = new Stream($unwritableHandle);
        $this->assertFalse($unwritableStream->isWritable());
    }

    public function testKnownLengthOfStreamIsAlwaysReturned(): void
    {
        $handle = fopen('php://temp', 'rb');
        $stream = new Stream($handle, 724);
        $this->assertEquals(724, $stream->getLength());
    }

    public function testNonResourceThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Stream(123);
    }

    public function testPositionReturnsCorrectPositionAfterWriting(): void
    {
        $handle = fopen('php://temp', 'w+b');
        $stream = new Stream($handle);
        $stream->write('foo');
        $this->assertEquals(3, $stream->getPosition());
    }

    public function testReadingFromClosedStreamThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $handle = fopen('php://temp', 'w+b');
        $stream = new Stream($handle);
        $stream->write('foo');
        $stream->close();
        $stream->read(1);
    }

    public function testReadingToEndFromClosedStreamThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $handle = fopen('php://temp', 'w+b');
        $stream = new Stream($handle);
        $stream->write('foo');
        $stream->close();
        $stream->readToEnd();
    }

    public function testReadingFromUnreadableStreamThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $handle = fopen(self::TEMP_FILE, 'ab');
        $stream = new Stream($handle);
        $stream->write('foo');
        $stream->read(1);
    }

    public function testReadingToEndFromUnreadableStreamThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $handle = fopen(self::TEMP_FILE, 'ab');
        $stream = new Stream($handle);
        $stream->write('foo');
        $stream->readToEnd();
    }

    public function testResourceLengthIsReturnedWhenLengthIsNotKnownAheadOfTime(): void
    {
        $handle = fopen('php://temp', 'rb');
        $expectedLength = fstat($handle)['size'];
        $stream = new Stream($handle);
        $this->assertEquals($expectedLength, $stream->getLength());
    }

    public function testRewindSeeksToBeginningOfStream(): void
    {
        $handle = fopen('php://temp', 'w+b');
        $stream = new Stream($handle);
        $stream->write('foo');
        $this->assertEquals('', $stream->readToEnd());
        $stream->rewind();
        $this->assertEquals('foo', $stream->readToEnd());
    }

    public function testSeekingChangesPosition(): void
    {
        $handle = fopen('php://temp', 'w+b');
        $stream = new Stream($handle);
        $stream->write('foo');
        $stream->rewind();
        $stream->seek(1);
        $this->assertEquals('oo', $stream->readToEnd());
        $stream->rewind();
        $stream->seek(2);
        $this->assertEquals('o', $stream->readToEnd());
    }

    public function testWritingToUnwritableStreamThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $handle = fopen('php://temp', 'rb');
        $stream = new Stream($handle);
        $stream->write('foo');
    }

    public function testWritingToStreamWritesData(): void
    {
        $handle = fopen('php://temp', 'w+b');
        $stream = new Stream($handle);
        $stream->write('foo');
        $stream->rewind();
        $this->assertEquals('foo', $stream->readToEnd());
    }
}

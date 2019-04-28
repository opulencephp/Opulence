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

    /**
     * Cleans up the tests
     */
    protected function tearDown(): void
    {
        if (file_exists(self::TEMP_FILE)) {
            @unlink(self::TEMP_FILE);
        }
    }

    /**
     * Tests that casting to a string on a closed stream returns an empty string
     */
    public function testCastingToStringOnClosedStreamReturnsEmptyString(): void
    {
        $handle = fopen('php://temp', 'w+b');
        $stream = new Stream($handle);
        $stream->write('foo');
        $stream->close();
        $this->assertEquals('', (string)$stream);
    }

    /**
     * Tests that casting to a string rewinds the stream and reads to end
     */
    public function testCastingToStringRewindsAndReadsToEnd(): void
    {
        $handle = fopen('php://temp', 'w+b');
        $stream = new Stream($handle);
        $stream->write('foo');
        $stream->read(1);
        $this->assertEquals('foo', (string)$stream);
    }

    /**
     * Tests that closing a stream unsets the resource
     */
    public function testClosingStreamUnsetsResource(): void
    {
        $handle = fopen('php://temp', 'rb');
        $stream = new Stream($handle);
        $stream->close();
        $this->assertFalse(is_resource($handle));
    }

    /**
     * Tests copying to a closed stream throws an exception
     */
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

    /**
     * Tests copying to a stream copies all its contents using the specified buffer size
     */
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

    /**
     * Tests that the destructor unsets the resource
     */
    public function testDestructorUnsetsResource(): void
    {
        $handle = fopen('php://temp', 'rb');
        $stream = new Stream($handle);
        unset($stream);
        $this->assertFalse(is_resource($handle));
    }

    /**
     * Tests that getting the length of a closed stream throws an exception
     */
    public function testGettingLengthOfClosedStreamThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $handle = fopen('php://temp', 'rb');
        $stream = new Stream($handle, 724);
        $stream->close();
        $stream->getLength();
    }

    /**
     * Tests that getting the position throws an exception if the stream is closed
     */
    public function testGettingPositionThrowsExceptionIfStreamIsClosed(): void
    {
        $this->expectException(RuntimeException::class);
        $handle = fopen('php://temp', 'rb');
        $stream = new Stream($handle);
        $stream->close();
        $stream->getPosition();
    }

    /**
     * Tests checking if we're at the end of the file returns false for streams that aren't at the end of file
     */
    public function testIsEofReturnsFalseForStreamsThatAreNotAtEof(): void
    {
        $handle = fopen('php://temp', 'w+b');
        $stream = new Stream($handle);
        $stream->write('foo');
        $this->assertFalse($stream->isEof());
    }

    /**
     * Tests checking if we're at the end of the file throws an exception on a closed stream
     */
    public function testIsEofThrowsExceptionForClosedStream(): void
    {
        $this->expectException(RuntimeException::class);
        $handle = fopen('php://temp', 'w+b');
        $stream = new Stream($handle);
        $stream->write('foo');
        $stream->close();
        $stream->isEof();
    }

    /**
     * Tests checking if we're at the end of the file returns true for streams at the end of file
     */
    public function testIsEofReturnsTrueForStreamsAtEof(): void
    {
        $handle = fopen('php://temp', 'w+b');
        $stream = new Stream($handle);
        $stream->write('foo');
        $stream->readToEnd();
        $this->assertTrue($stream->isEof());
    }

    /**
     * Tests that checking if a stream is readable returns the correct value based on its mode
     */
    public function testIsReadableReturnsCorrectValueBasedOnItsMode(): void
    {
        $readableHandle = fopen('php://temp', 'rb');
        $readableStream = new Stream($readableHandle);
        $this->assertTrue($readableStream->isReadable());
        $unreadableHandle = fopen(self::TEMP_FILE, 'wb');
        $unreadableStream = new Stream($unreadableHandle);
        $this->assertFalse($unreadableStream->isReadable());
    }

    /**
     * Tests that checking if a stream is seekable returns the correct value based on its mode
     */
    public function testIsSeekableReturnsCorrectValueBasedOnItsMode(): void
    {
        $seekableHandle = fopen('php://temp', 'r+b');
        $seekableStream = new Stream($seekableHandle);
        $this->assertTrue($seekableStream->isSeekable());
        // Testing unseekable streams is not possible
    }

    /**
     * Tests that checking if a stream is writable returns the correct value based on its mode
     */
    public function testIsWritableableReturnsCorrectValueBasedOnItsMode(): void
    {
        $writableHandle = fopen('php://temp', 'wb');
        $writableStream = new Stream($writableHandle);
        $this->assertTrue($writableStream->isWritable());
        $unwritableHandle = fopen('php://temp', 'rb');
        $unwritableStream = new Stream($unwritableHandle);
        $this->assertFalse($unwritableStream->isWritable());
    }

    /**
     * Tests that the known length of a stream is always returned
     */
    public function testKnownLengthOfStreamIsAlwaysReturned(): void
    {
        $handle = fopen('php://temp', 'rb');
        $stream = new Stream($handle, 724);
        $this->assertEquals(724, $stream->getLength());
    }

    /**
     * Tests that an invalid stream throws an exception
     */
    public function testNonResourceThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Stream(123);
    }

    /**
     * Tests that the position returns the correct position after writing
     */
    public function testPositionReturnsCorrectPositionAfterWriting(): void
    {
        $handle = fopen('php://temp', 'w+b');
        $stream = new Stream($handle);
        $stream->write('foo');
        $this->assertEquals(3, $stream->getPosition());
    }

    /**
     * Tests that reading from a closed stream returns null
     */
    public function testReadingFromClosedStreamThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $handle = fopen('php://temp', 'w+b');
        $stream = new Stream($handle);
        $stream->write('foo');
        $stream->close();
        $stream->read(1);
    }

    /**
     * Tests that reading to the end from a closed stream returns null
     */
    public function testReadingToEndFromClosedStreamThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $handle = fopen('php://temp', 'w+b');
        $stream = new Stream($handle);
        $stream->write('foo');
        $stream->close();
        $stream->readToEnd();
    }

    /**
     * Tests that reading from an unreadable stream throws an exception
     */
    public function testReadingFromUnreadableStreamThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $handle = fopen(self::TEMP_FILE, 'ab');
        $stream = new Stream($handle);
        $stream->write('foo');
        $stream->read(1);
    }

    /**
     * Tests that reading to the end from an unreadable stream throws an exception
     */
    public function testReadingToEndFromUnreadableStreamThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $handle = fopen(self::TEMP_FILE, 'ab');
        $stream = new Stream($handle);
        $stream->write('foo');
        $stream->readToEnd();
    }

    /**
     * Tests that the resource's length is returned when the length is not known ahead of time
     */
    public function testResourceLengthIsReturnedWhenLengthIsNotKnownAheadOfTime(): void
    {
        $handle = fopen('php://temp', 'rb');
        $expectedLength = fstat($handle)['size'];
        $stream = new Stream($handle);
        $this->assertEquals($expectedLength, $stream->getLength());
    }

    /**
     * Tests that rewinding seeks to the beginning of the stream
     */
    public function testRewindSeeksToBeginningOfStream(): void
    {
        $handle = fopen('php://temp', 'w+b');
        $stream = new Stream($handle);
        $stream->write('foo');
        $this->assertEquals('', $stream->readToEnd());
        $stream->rewind();
        $this->assertEquals('foo', $stream->readToEnd());
    }

    /**
     * Tests that seeking changes the position
     */
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

    /**
     * Tests that writing to an unwritable stream throws an exception
     */
    public function testWritingToUnwritableStreamThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $handle = fopen('php://temp', 'rb');
        $stream = new Stream($handle);
        $stream->write('foo');
    }

    /**
     * Tests that writing to the stream actually writes data
     */
    public function testWritingToStreamWritesData(): void
    {
        $handle = fopen('php://temp', 'w+b');
        $stream = new Stream($handle);
        $stream->write('foo');
        $stream->rewind();
        $this->assertEquals('foo', $stream->readToEnd());
    }
}

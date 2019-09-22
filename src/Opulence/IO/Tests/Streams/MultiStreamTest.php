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
use Opulence\IO\Streams\IStream;
use Opulence\IO\Streams\MultiStream;
use Opulence\IO\Streams\Stream;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;

/**
 * Tests the multi-stream
 */
class MultiStreamTest extends \PHPUnit\Framework\TestCase
{
    private MultiStream $multiStream;

    protected function setUp(): void
    {
        $this->multiStream = new MultiStream();
    }

    public function testAddChecksThatTheStreamIsReadable(): void
    {
        $stream = $this->createReadableStream();
        $this->multiStream->addStream($stream);
    }

    public function testAddingUnreadableStreamThrowsAnException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $unreadableStream = $this->createMock(IStream::class);
        $unreadableStream->expects($this->once())
            ->method('isReadable')
            ->willReturn(false);
        $this->multiStream->addStream($unreadableStream);
    }

    public function testClosingStreamMakesItSeekableAgainAndResetsThePosition(): void
    {
        $unseekableStream = $this->createReadableStream();
        $unseekableStream->expects($this->once())
            ->method('isSeekable')
            ->willReturn(false);
        $this->multiStream->addStream($unseekableStream);
        $this->multiStream->close();
        $this->assertTrue($this->multiStream->isSeekable());
        $this->assertEquals(0, $this->multiStream->getPosition());
    }

    public function testClosingStreamUnsetsSubstreamResources(): void
    {
        $handle1 = fopen('php://temp', 'rb');
        $stream1 = new Stream($handle1);
        $handle2 = fopen('php://memory', 'rb');
        $stream2 = new Stream($handle2);
        $this->multiStream->addStream($stream1);
        $this->multiStream->addStream($stream2);
        $this->multiStream->close();
        $this->assertFalse(is_resource($handle1));
        $this->assertFalse(is_resource($handle2));
    }

    public function testCopyingToClosedStreamThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $stream = new Stream(fopen('php://temp', 'r+b'));
        $stream->write('foo');
        $stream->rewind();
        $this->multiStream->addStream($stream);
        $destinationStream = new Stream(fopen('php://temp', 'r+b'));
        $destinationStream->close();
        $this->multiStream->copyToStream($destinationStream, 1);
    }

    public function testCopyingToStreamCopiesAllContentsUsingBufferSize(): void
    {
        $stream1 = new Stream(fopen('php://temp', 'r+b'));
        $stream2 = new Stream(fopen('php://temp', 'r+b'));
        $stream1->write('foo');
        $stream1->write('bar');
        $stream1->rewind();
        $stream2->rewind();
        $this->multiStream->addStream($stream1);
        $this->multiStream->addStream($stream2);
        $destinationStream = new Stream(fopen('php://temp', 'r+b'));
        $this->multiStream->copyToStream($destinationStream, 1);
        $destinationStream->rewind();
        $this->assertEquals('foobar', $destinationStream->readToEnd());
    }

    public function testDestroyingStreamUnsetsSubstreamResources(): void
    {
        $handle1 = fopen('php://temp', 'rb');
        $stream1 = new Stream($handle1);
        $handle2 = fopen('php://memory', 'rb');
        $stream2 = new Stream($handle2);
        $this->multiStream->addStream($stream1);
        $this->multiStream->addStream($stream2);
        unset($this->multiStream);
        $this->assertFalse(is_resource($handle1));
        $this->assertFalse(is_resource($handle2));
    }

    public function testEofOnlyReturnsTrueIfLastStreamIsAtEof(): void
    {
        $stream1 = new Stream(fopen('php://temp', 'r+b'));
        $stream2 = new Stream(fopen('php://temp', 'r+b'));
        $stream1->write('foo');
        $stream1->rewind();
        $stream2->write('bar');
        $stream2->rewind();
        $this->multiStream->addStream($stream1);
        $this->multiStream->addStream($stream2);
        // Test that it returns false when not on the last stream
        $this->assertFalse($this->multiStream->isEof());
        // Test that it returns false when on the last stream, but that stream isn't at EOF
        $this->multiStream->read(3);
        $this->assertFalse($this->multiStream->isEof());
        // Test that it returns true when on the last stream, and that stream is at EOF
        $this->multiStream->read(3);
        // Read one additional char to get to the EOF
        $this->multiStream->read(1);
        $this->assertTrue($this->multiStream->isEof());
    }

    public function testEofThrowsExceptionWithNoStreams(): void
    {
        $this->expectException(RuntimeException::class);
        $this->multiStream->isEof();
    }

    public function testGettingLengthWillReturnNullIfAnyStreamsHaveNullLength(): void
    {
        $streamWithLength = $this->createReadableStream();
        $streamWithoutLength = $this->createReadableStream();
        $streamWithLength->expects($this->once())
            ->method('getLength')
            ->willReturn(10);
        $streamWithoutLength->expects($this->once())
            ->method('getLength')
            ->willReturn(null);
        $this->multiStream->addStream($streamWithLength);
        $this->multiStream->addStream($streamWithoutLength);
        $this->assertNull($this->multiStream->getLength());
    }

    public function testGettingLengthWillSumLengthsOfStreams(): void
    {
        $stream1 = $this->createReadableStream();
        $stream2 = $this->createReadableStream();
        $stream1->expects($this->once())
            ->method('getLength')
            ->willReturn(10);
        $stream2->expects($this->once())
            ->method('getLength')
            ->willReturn(20);
        $this->multiStream->addStream($stream1);
        $this->multiStream->addStream($stream2);
        $this->assertEquals(30, $this->multiStream->getLength());
    }

    public function testGettingLengthWithoutAnySubstreamsReturnsNull(): void
    {
        $this->assertNull($this->multiStream->getLength());
    }

    public function testIsReadableAlwaysReturnsTrue(): void
    {
        $this->assertTrue($this->multiStream->isReadable());
    }

    public function testIsSeekableOnlyReturnsTrueIfAllStreamsAreSeekable(): void
    {
        $this->assertTrue($this->multiStream->isReadable());
    }

    public function testIsWritableAlwaysReturnsFalse(): void
    {
        $seekableStream = $this->createReadableStream();
        $unseekableStream = $this->createReadableStream();
        $seekableStream->expects($this->once())
            ->method('isSeekable')
            ->willReturn(true);
        $unseekableStream->expects($this->once())
            ->method('isSeekable')
            ->willReturn(false);
        $this->multiStream->addStream($seekableStream);
        $this->assertTrue($this->multiStream->isSeekable());
        $this->multiStream->addStream($unseekableStream);
        $this->assertFalse($this->multiStream->isSeekable());
    }

    public function testReadingEmptyStreamReturnsEmptyString(): void
    {
        $this->assertEquals('', $this->multiStream->read(123));
    }

    public function testReadingFromMulitpleStreamsReadsFirstToEofAndRemainderFromSecond(): void
    {
        $stream1 = $this->createReadableStream();
        $stream2 = $this->createReadableStream();
        $stream1->expects($this->once())
            ->method('read')
            ->with(3)
            ->willReturn('fo');
        $stream2->expects($this->once())
            ->method('read')
            ->with(1)
            ->willReturn('o');
        $this->multiStream->addStream($stream1);
        $this->multiStream->addStream($stream2);
        $this->assertEquals('foo', $this->multiStream->read(3));
        $this->assertEquals(3, $this->multiStream->getPosition());
    }

    public function testReadingFromSingleStreamReadsThatStream(): void
    {
        $stream = $this->createReadableStream();
        $stream->expects($this->once())
            ->method('read')
            ->with(3)
            ->willReturn('foo');
        $this->multiStream->addStream($stream);
        $this->assertEquals('foo', $this->multiStream->read(3));
    }

    public function testReadingToEndWithMultipleStreamsReadsFromCurrentPositionToEnd(): void
    {
        $stream1 = new Stream(fopen('php://temp', 'r+b'));
        $stream2 = new Stream(fopen('php://temp', 'r+b'));
        $stream1->write('abc');
        $stream2->write('de');
        $this->multiStream->addStream($stream1);
        $this->multiStream->addStream($stream2);
        $this->multiStream->rewind();
        $this->assertEquals('abcde', $this->multiStream->readToEnd());
        $this->assertTrue($this->multiStream->isEof());
        $this->multiStream->seek(1);
        $this->assertEquals('bcde', $this->multiStream->readToEnd());
        $this->assertTrue($this->multiStream->isEof());
    }

    public function testReadingToEndWithNoStreamsReturnsEmptyString(): void
    {
        $this->assertEquals('', $this->multiStream->readToEnd());
    }

    public function testReadingToEndWithSingleStreamReadsItToEnd(): void
    {
        $stream = new Stream(fopen('php://temp', 'r+b'));
        $stream->write('foo');
        $this->multiStream->addStream($stream);
        $this->multiStream->seek(1);
        $this->assertEquals('oo', $this->multiStream->readToEnd());
        $this->assertTrue($this->multiStream->isEof());
    }

    public function testSeekingFromEndWhenLengthIsNotKnownThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $stream = $this->createReadableStream();
        $stream->expects($this->any())
            ->method('getLength')
            ->willReturn(null);
        $this->multiStream->addStream($stream);
        $this->multiStream->seek(-1, SEEK_END);
    }

    public function testSeekingWithMultipleStreamsSeeksToCorrectPosition(): void
    {
        $stream1 = new Stream(fopen('php://temp', 'r+b'));
        $stream2 = new Stream(fopen('php://temp', 'r+b'));
        $stream3 = new Stream(fopen('php://temp', 'r+b'));
        $stream1->write('abc');
        $stream2->write('de');
        $stream3->write('fghij');
        $this->multiStream->addStream($stream1);
        $this->multiStream->addStream($stream2);
        $this->multiStream->addStream($stream3);

        $this->multiStream->seek(1);
        $this->assertEquals(1, $stream1->getPosition());
        $this->assertEquals(0, $stream2->getPosition());
        $this->assertEquals(0, $stream3->getPosition());

        $this->multiStream->seek(3);
        $this->assertEquals(3, $stream1->getPosition());
        $this->assertEquals(0, $stream2->getPosition());
        $this->assertEquals(0, $stream3->getPosition());

        $this->multiStream->seek(4);
        $this->assertEquals(3, $stream1->getPosition());
        $this->assertEquals(1, $stream2->getPosition());
        $this->assertEquals(0, $stream3->getPosition());

        $this->multiStream->seek(5);
        $this->assertEquals(3, $stream1->getPosition());
        $this->assertEquals(2, $stream2->getPosition());
        $this->assertEquals(0, $stream3->getPosition());

        $this->multiStream->seek(6);
        $this->assertEquals(3, $stream1->getPosition());
        $this->assertEquals(2, $stream2->getPosition());
        $this->assertEquals(1, $stream3->getPosition());
    }

    public function testSeekingWithSingleStreamSeeksToCorrectPosition(): void
    {
        $stream = new Stream(fopen('php://temp', 'r+b'));
        $stream->write('foobar');
        $this->multiStream->addStream($stream);
        $this->multiStream->seek(1);
        $this->assertEquals(1, $stream->getPosition());
        $this->multiStream->seek(2, SEEK_CUR);
        $this->assertEquals(3, $stream->getPosition());
        $this->multiStream->seek(-1, SEEK_END);
        $this->assertEquals(5, $stream->getPosition());
    }

    public function testSeekingStreamWithUnknownLengthThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $stream = $this->createReadableStream();
        $stream->expects($this->any())
            ->method('getLength')
            ->willReturn(null);
        $this->multiStream->addStream($stream);
        $this->multiStream->seek(1);
    }

    public function testSeekingUnseekableStreamThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $unseekableStream = $this->createReadableStream();
        $unseekableStream->expects($this->once())
            ->method('isSeekable')
            ->willReturn(false);
        $this->multiStream->addStream($unseekableStream);
        $this->multiStream->seek(0);
    }

    public function testToStringRewindsStreamsAndReadsThemToTheEnd(): void
    {
        $stream1 = new Stream(fopen('php://temp', 'r+b'));
        $stream2 = new Stream(fopen('php://temp', 'r+b'));
        $stream1->write('foo');
        $stream2->write('bar');
        $stream1->seek(1);
        $stream2->seek(1);
        $this->multiStream->addStream($stream1);
        $this->multiStream->addStream($stream2);
        $this->assertEquals('foobar', (string)$this->multiStream);
    }

    public function testWritingThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->multiStream->write('foo');
    }

    /**
     * Creates a readable stream
     *
     * @return IStream|MockObject The readable stream
     */
    private function createReadableStream(): IStream
    {
        $stream = $this->createMock(IStream::class);
        $stream->expects($this->once())
            ->method('isReadable')
            ->willReturn(true);

        return $stream;
    }
}

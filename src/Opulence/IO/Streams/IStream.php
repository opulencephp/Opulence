<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\IO\Streams;

use RuntimeException;

/**
 * Defines the interface for streams to implement
 */
interface IStream
{
    /**
     * Rewinds the stream and reads it to the end as a string
     * This could result in a lot of data being loaded into memory
     *
     * @return string The entire stream as a string
     */
    public function __toString(): string;

    /**
     * Closes the stream
     *
     * @throws RuntimeException Thrown if the stream failed to be closed
     */
    public function close(): void;

    /**
     * Copies this stream to another
     *
     * @param IStream $stream The stream to copy to
     * @param int $bufferSize The buffer size to use when copying, if needed
     * @throws RuntimeException Thrown if the source stream is closed
     */
    public function copyToStream(IStream $stream, int $bufferSize = 8192): void;

    /**
     * Gets the length of the stream
     *
     * @return int|null The length of the stream if knowable, otherwise null
     * @throws RuntimeException Thrown if the stream is closed
     */
    public function getLength(): ?int;

    /**
     * Gets the current stream position
     *
     * @return int The current position of the stream
     * @throws RuntimeException Thrown if the position cannot be determined
     */
    public function getPosition(): int;

    /**
     * Gets whether or not the stream is at the end of file
     *
     * @return bool True if the stream is end of file, otherwise false
     * @throws RuntimeException Thrown if the stream is closed or EOF cannot be determined
     */
    public function isEof(): bool;

    /**
     * Gets whether or not the stream is readable
     *
     * @return bool True if the stream is readable, otherwise false
     */
    public function isReadable(): bool;

    /**
     * Gets whether or not the stream is seekable
     *
     * @return bool True if the stream is seekable, otherwise false
     */
    public function isSeekable(): bool;

    /**
     * Gets whether or not the stream is writable
     *
     * @return bool True if the stream is writable, otherwise false
     */
    public function isWritable(): bool;

    /**
     * Reads a chunk of the stream
     *
     * @param int $length The number of bytes to read
     * @return string The stream contents as a string
     * @throws RuntimeException Thrown if the stream is not readable
     */
    public function read(int $length): string;

    /**
     * Reads to the end of the stream
     *
     * @return string The stream contents as a string
     * @throws RuntimeException Thrown if the stream is not readable
     */
    public function readToEnd(): string;

    /**
     * Rewinds to the beginning of the stream
     *
     * @throws RuntimeException Thrown if the stream is not seekable
     */
    public function rewind(): void;

    /**
     * Seeks to a certain position in the stream
     *
     * @param int $offset The offset to seek to
     * @param int $whence How the position will be calculated from the offset (identical to fseek())
     * @throws RuntimeException Thrown if the stream is not seekable
     */
    public function seek(int $offset, int $whence = SEEK_SET): void;

    /**
     * Writes to the stream
     *
     * @param string $data The data to write
     * @throws RuntimeException Thrown if the stream is not writable
     */
    public function write(string $data): void;
}

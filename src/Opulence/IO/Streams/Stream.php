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

use InvalidArgumentException;
use RuntimeException;

/**
 * Defines a basic stream
 */
class Stream implements IStream
{
    /** @var array The list of readable stream modes */
    private static $readStreamModes = [
        'a+',
        'c+',
        'c+b',
        'c+t',
        'r',
        'rb',
        'rt',
        'r+',
        'r+b',
        'r+t',
        'w+',
        'w+b',
        'w+t',
        'x+',
        'x+b',
        'x+t'
    ];
    /** @var array The list of writable stream modes */
    private static $writeStreamModes = [
        'a',
        'a+',
        'c+',
        'c+b',
        'c+t',
        'rw',
        'r+',
        'r+b',
        'r+t',
        'w',
        'wb',
        'w+',
        'w+b',
        'w+t',
        'x+',
        'x+b',
        'x+t'
    ];
    /** @var resource The underlying stream handle */
    private $handle;
    /** @var int|null The length of the stream, if known */
    private $length;
    /** @var bool Whether or not the stream is readable */
    private $isReadable;
    /** @var bool Whether or not the stream is seekable */
    private $isSeekable;
    /** @var bool Whether or not the stream is writable */
    private $isWritable;

    /**
     * @param resource $handle The underlying stream handle
     * @param int|null The length of the stream, if known
     */
    public function __construct($handle, ?int $length = null)
    {
        if (!is_resource($handle)) {
            throw new InvalidArgumentException('Stream must be a resource');
        }

        $this->handle = $handle;
        $this->length = $length;
        $streamMetadata = \stream_get_meta_data($this->handle);
        $this->isReadable = in_array($streamMetadata['mode'], self::$readStreamModes, true);
        $this->isSeekable = $streamMetadata['seekable'];
        $this->isWritable = in_array($streamMetadata['mode'], self::$writeStreamModes, true);
    }

    /**
     * Closes the stream
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        try {
            $this->rewind();

            return $this->readToEnd();
        } catch (RuntimeException $ex) {
            return '';
        }
    }

    /**
     * @inheritdoc
     */
    public function close(): void
    {
        if (\is_resource($this->handle) && \fclose($this->handle) === false) {
            throw new RuntimeException('Failed to close stream');
        }

        $this->handle = null;
        $this->length = null;
        $this->isReadable = false;
        $this->isSeekable = false;
        $this->isWritable = false;
    }

    /**
     * @inheritdoc
     */
    public function copyToStream(IStream $stream, int $bufferSize = 8192): void
    {
        while (!$this->isEof()) {
            $stream->write($this->read($bufferSize));
        }
    }

    /**
     * @inheritdoc
     */
    public function getLength(): ?int
    {
        // Handle a closed stream
        if ($this->handle === null) {
            throw new RuntimeException('Unable to get size of closed stream');
        }

        if ($this->length !== null) {
            return $this->length;
        }

        $fileStats = \fstat($this->handle);

        if (isset($fileStats['size'])) {
            $this->length = $fileStats['size'];

            return $this->length;
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getPosition(): int
    {
        if (!\is_resource($this->handle)) {
            throw new RuntimeException('Unable to get position of closed stream');
        }

        if (($position = \ftell($this->handle)) === false) {
            throw new RuntimeException('Failed to get position of stream');
        }

        return $position;
    }

    /**
     * @inheritdoc
     */
    public function isEof(): bool
    {
        if (!\is_resource($this->handle)) {
            throw new RuntimeException('Unable to tell if at EOF on closed stream');
        }

        return \feof($this->handle);
    }

    /**
     * @inheritdoc
     */
    public function isReadable(): bool
    {
        return $this->isReadable;
    }

    /**
     * @inheritdoc
     */
    public function isSeekable(): bool
    {
        return $this->isSeekable;
    }

    /**
     * @inheritdoc
     */
    public function isWritable(): bool
    {
        return $this->isWritable;
    }

    /**
     * @inheritdoc
     */
    public function read(int $length): string
    {
        if (!$this->isReadable) {
            throw new RuntimeException('Stream is not readable');
        }

        if (($content = \fread($this->handle, $length)) === false) {
            throw new RuntimeException('Failed to read stream');
        }

        return $content;
    }

    /**
     * @inheritdoc
     */
    public function readToEnd(): string
    {
        if (!$this->isReadable) {
            throw new RuntimeException('Stream is not readable');
        }

        if (($content = \stream_get_contents($this->handle)) === false) {
            throw new RuntimeException('Failed to read stream');
        }

        return $content;
    }

    /**
     * @inheritdoc
     */
    public function rewind(): void
    {
        $this->seek(0);
    }

    /**
     * @inheritdoc
     */
    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!$this->isSeekable || \fseek($this->handle, $offset, $whence)) {
            throw new RuntimeException('Stream is not seekable');
        }

        if (\fseek($this->handle, $offset, $whence) === -1) {
            throw new RuntimeException('Error while seeking stream');
        }
    }

    /**
     * @inheritdoc
     */
    public function write(string $data): void
    {
        if (!$this->isWritable) {
            throw new RuntimeException('Stream is not writable');
        }

        if (\fwrite($this->handle, $data) === false) {
            throw new RuntimeException('Failed to write to stream');
        }

        // Reset the length, which if knowable will be recalculated next time getLength() is called
        $this->length = null;
    }
}

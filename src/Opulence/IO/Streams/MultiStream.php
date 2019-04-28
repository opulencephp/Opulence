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
 * Defines a stream that contains multiple sub-streams
 */
class MultiStream implements IStream
{
    /** @var IStream[] The list of sub-streams */
    private $streams = [];
    /** @var bool Whether or not this stream is seekable */
    private $isSeekable = true;
    /** @var int The index of the currently read stream */
    private $streamIndex = 0;
    /** @var int The current position inside the substreams */
    private $position = 0;

    /**
     * @param IStream $streams The list of streams to add
     */
    public function __construct(array $streams = [])
    {
        foreach ($streams as $stream) {
            $this->addStream($stream);
        }
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
     * Adds a stream to the multi-stream
     *
     * @param IStream $stream The stream to add
     * @throws InvalidArgumentException Thrown if the stream is not readable
     */
    public function addStream(IStream $stream): void
    {
        if (!$stream->isReadable()) {
            throw new InvalidArgumentException('Stream must be readable');
        }

        $this->isSeekable = $this->isSeekable && $stream->isSeekable();
        $this->streams[] = $stream;
    }

    /**
     * @inheritdoc
     */
    public function close(): void
    {
        $this->streamIndex = 0;
        $this->position = 0;
        $this->isSeekable = true;

        foreach ($this->streams as $stream) {
            $stream->close();
        }

        $this->streams = [];
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
        if (count($this->streams) === 0) {
            return null;
        }

        $length = 0;

        foreach ($this->streams as $stream) {
            if (($substreamLength = $stream->getLength()) === null) {
                return null;
            }

            $length += $substreamLength;
        }

        return $length;
    }

    /**
     * @inheritdoc
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @inheritdoc
     */
    public function isEof(): bool
    {
        if (count($this->streams) === 0) {
            throw new RuntimeException('Unable to tell if at EOF on closed stream');
        }

        return $this->streamIndex === count($this->streams) - 1 && $this->streams[$this->streamIndex]->isEof();
    }

    /**
     * @inheritdoc
     */
    public function isReadable(): bool
    {
        return true;
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
        return false;
    }

    /**
     * @inheritdoc
     */
    public function read(int $length): string
    {
        if (count($this->streams) === 0) {
            return '';
        }

        $remainingLength = $length;
        $buffer = '';

        while ($remainingLength > 0) {
            $currStreamBuffer = $this->streams[$this->streamIndex]->read($remainingLength);
            $remainingLength -= strlen($currStreamBuffer);
            $buffer .= $currStreamBuffer;

            if ($this->streamIndex === count($this->streams) - 1) {
                break;
            }

            if ($remainingLength > 0) {
                $this->streamIndex++;
            }
        }

        $this->position = strlen($buffer);

        return $buffer;
    }

    /**
     * @inheritdoc
     */
    public function readToEnd(): string
    {
        if (count($this->streams) === 0) {
            return '';
        }

        $buffer = '';

        // We don't use a for loop because it complicates $this->streamIndex on the last iteration
        foreach ($this->streams as $streamIndex => $stream) {
            $buffer .= $stream->readToEnd();
            $this->streamIndex = $streamIndex;
        }

        $this->position = $this->getLength();

        return $buffer;
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
     * @throws InvalidArgumentException Thrown if the whence is invalid
     */
    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!$this->isSeekable) {
            throw new RuntimeException('Cannot seek an unseekable stream');
        }

        if ($this->getLength() === null) {
            throw new RuntimeException('Cannot seek a stream whose length is not known');
        }

        switch ($whence) {
            case SEEK_CUR:
                $this->position += $offset;
                break;
            case SEEK_END:
                $this->position = $this->getLength() + $offset;
                break;
            case SEEK_SET:
                $this->position = $offset;
                break;
            default:
                throw new InvalidArgumentException("Whence $whence is invalid");
        }

        $currPosition = 0;

        // We don't use a for loop because it complicates $this->streamIndex on the last iteration
        foreach ($this->streams as $streamIndex => $stream) {
            $this->streamIndex = $streamIndex;
            $currStreamLength = $stream->getLength();

            // Check if this is the stream that contains the desired offset
            if ($this->position < $currPosition + $currStreamLength) {
                $stream->seek($this->position - $currPosition);

                // Rewind the remaining streams
                for ($remainingIndex = $this->streamIndex + 1;$remainingIndex < count($this->streams);$remainingIndex++) {
                    $this->streams[$remainingIndex]->rewind();
                }

                return;
            } else {
                // Move this stream to the end
                $stream->seek(0, SEEK_END);
                $currPosition += $currStreamLength;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function write(string $data): void
    {
        throw new RuntimeException('Cannot write to ' . self::class);
    }
}

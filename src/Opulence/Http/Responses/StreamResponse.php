<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Http\Responses;

use LogicException;

/**
 * Defines a stream response whose contents are only output once
 */
class StreamResponse extends Response
{
    /** @var callable The stream callback */
    protected $streamCallback = null;
    /** @var bool Whether or not we've sent the stream */
    protected $hasSentStream = false;

    /**
     * @inheritdoc
     * @param callable $streamCallback The callback that streams/outputs the content
     */
    public function __construct(
        callable $streamCallback = null,
        int $statusCode = ResponseHeaders::HTTP_OK,
        array $headers = []
    ) {
        parent::__construct("", $statusCode, $headers);

        if ($streamCallback !== null) {
            $this->setStreamCallback($streamCallback);
        }
    }

    /**
     * @inheritdoc
     */
    public function sendContent()
    {
        if (!$this->hasSentStream && $this->streamCallback !== null) {
            ($this->streamCallback)();
            $this->hasSentStream = true;
        }
    }

    /**
     * @inheritdoc
     * @throws LogicException Thrown because you cannot set content of a stream response
     */
    public function setContent($content)
    {
        if ($content !== null && $content !== "") {
            throw new LogicException("Cannot set content in a stream response");
        }
    }

    /**
     * Sets the stream callback
     *
     * @param callable $streamCallback
     */
    public function setStreamCallback(callable $streamCallback)
    {
        $this->streamCallback = $streamCallback;
    }
}
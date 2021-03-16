<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Http;

use Exception;

/**
 * Defines an exception that is thrown by an HTTP component
 */
class HttpException extends Exception
{
    /** @var int The HTTP status code */
    private $statusCode = 200;
    /** @var array The list of headers to include */
    private $headers = [];

    /**
     * @inheritdoc
     * @param int $statusCode The HTTP status code
     * @param array $headers The HTTP headers
     */
    public function __construct(
        int $statusCode,
        string $message = '',
        array $headers = [],
        int $code = 0,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * @return array
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * @return int
     */
    public function getStatusCode() : int
    {
        return $this->statusCode;
    }
}

<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Http\Responses;

use DateTime;

/**
 * Defines an HTTP response
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Response_codes
 */
class Response
{
    /** @var mixed The content of the response */
    protected $content = '';
    /** @var ResponseHeaders The headers in this response */
    protected $headers = null;
    /** @var int The status code of this response */
    protected $statusCode = ResponseHeaders::HTTP_OK;
    /** @var string The status text of this response */
    protected $statusText = 'OK';
    /** @var string The HTTP version of this response */
    protected $httpVersion = '1.1';

    /**
     * @param mixed $content The content of the response
     * @param int $statusCode The HTTP status code
     * @param array $headers The headers to set
     */
    public function __construct($content = '', int $statusCode = ResponseHeaders::HTTP_OK, array $headers = [])
    {
        $this->setContent($content);
        $this->headers = new ResponseHeaders($headers);
        $this->setStatusCode($statusCode);
    }

    /**
     * @return string
     */
    public function getContent() : string
    {
        return $this->content;
    }

    /**
     * @return ResponseHeaders
     */
    public function getHeaders() : ResponseHeaders
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getHttpVersion() : string
    {
        return $this->httpVersion;
    }

    /**
     * @return int
     */
    public function getStatusCode() : int
    {
        return $this->statusCode;
    }

    /**
     * Gets whether or not the headers have been sent
     *
     * @return bool True if they've been sent, otherwise false
     */
    public function headersAreSent() : bool
    {
        return headers_sent();
    }

    /**
     * Sends the headers and content
     */
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
        // To prevent any potential output buffering, let's flush
        flush();
    }

    /**
     * Sends the content
     */
    public function sendContent()
    {
        if (!$this->headersAreSent()) {
            echo $this->content;
        }
    }

    /**
     * Sends the headers if they haven't already been sent
     */
    public function sendHeaders()
    {
        if (!$this->headersAreSent()) {
            header(
                sprintf(
                    'HTTP/%s %s %s',
                    $this->httpVersion,
                    $this->statusCode,
                    $this->statusText
                ),
                true,
                $this->statusCode
            );

            // Send the headers
            foreach ($this->headers->getAll() as $name => $values) {
                // Headers are allowed to have multiple values
                foreach ($values as $value) {
                    header("$name:$value", false, $this->statusCode);
                }
            }

            // Send the cookies
            /** @var Cookie $cookie */
            foreach ($this->headers->getCookies(true) as $cookie) {
                setcookie(
                    $cookie->getName(),
                    $cookie->getValue(),
                    $cookie->getExpiration(),
                    $cookie->getPath(),
                    $cookie->getDomain(),
                    $cookie->isSecure(),
                    $cookie->isHttpOnly()
                );
            }
        }
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Sets the expiration time of the page
     *
     * @param DateTime $expiration The expiration time
     */
    public function setExpiration(DateTime $expiration)
    {
        $this->headers->set('Expires', $expiration->format('r'));
    }

    /**
     * @param string $httpVersion
     */
    public function setHttpVersion(string $httpVersion)
    {
        $this->httpVersion = $httpVersion;
    }

    /**
     * Sets the status code
     *
     * @param int $statusCode The HTTP status code
     * @param string|null $statusText The status text
     *      If null, the default text is used for the input code
     */
    public function setStatusCode(int $statusCode, string $statusText = null)
    {
        $this->statusCode = $statusCode;

        if ($statusText === null && isset(ResponseHeaders::$statusTexts[$statusCode])) {
            $this->statusText = ResponseHeaders::$statusTexts[$statusCode];
        } else {
            $this->statusText = (string)$statusText;
        }
    }
}

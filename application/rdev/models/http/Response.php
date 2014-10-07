<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an HTTP response
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Response_codes
 */
namespace RDev\Models\HTTP;

class Response
{
    /** @var string The content of the response */
    protected $content = "";
    /** @var ResponseHeaders The headers in this response */
    protected $headers = null;
    /** @var int The status code of this response */
    protected $statusCode = ResponseHeaders::HTTP_OK;
    /** @var string The status text of this response */
    protected $statusText = "OK";
    /** @var string The status text of this response */
    /** @var string The HTTP version of this response */
    protected $httpVersion = "1.1";

    /**
     * @param string $content The content of the response
     * @param int $statusCode The HTTP status code
     * @param array $headers The headers to set
     */
    public function __construct($content = "", $statusCode = ResponseHeaders::HTTP_OK, array $headers = [])
    {
        $this->setContent($content);
        $this->headers = new ResponseHeaders($headers);
        $this->setStatusCode($statusCode);
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getHTTPVersion()
    {
        return $this->httpVersion;
    }

    /**
     * @return ResponseHeaders
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Gets whether or not the headers have been sent
     *
     * @return bool True if they've been sent, otherwise false
     */
    public function headersAreSent()
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
    }

    /**
     * Sends the content
     */
    public function sendContent()
    {
        if(!$this->headersAreSent())
        {
            echo $this->content;
        }
    }

    /**
     * Sends the headers if they haven't already been sent
     */
    public function sendHeaders()
    {
        if(!$this->headersAreSent())
        {
            header(
                sprintf(
                    "HTTP/%s %s %s",
                    $this->httpVersion,
                    $this->statusCode,
                    $this->statusText
                ),
                true,
                $this->statusCode
            );

            // Send the headers
            foreach($this->headers->getAll() as $name => $values)
            {
                // Headers are allowed to have multiple values
                foreach($values as $value)
                {
                    header($name . ":" . $value, false, $this->statusCode);
                }
            }

            // Send the cookies
            /** @var Cookie $cookie */
            foreach($this->headers->getCookies() as $cookie)
            {
                setcookie(
                    $cookie->getName(),
                    $cookie->getValue(),
                    $cookie->getExpiration()->format("U"),
                    $cookie->getPath(),
                    $cookie->getDomain(),
                    $cookie->isSecure(),
                    $cookie->isHTTPOnly()
                );
            }
        }
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Sets the expiration time of the page
     *
     * @param \DateTime $expiration The expiration time
     * @throws Exceptions\HTTPException Thrown if the headers were already sent
     */
    public function setExpiration(\DateTime $expiration)
    {
        $this->headers->set("Expires", $expiration->format("r"));
    }

    /**
     * @param string $httpVersion
     */
    public function setHTTPVersion($httpVersion)
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
    public function setStatusCode($statusCode, $statusText = null)
    {
        $this->statusCode = $statusCode;

        if($statusText === null && isset(ResponseHeaders::$statusTexts[$statusCode]))
        {
            $this->statusText = ResponseHeaders::$statusTexts[$statusCode];
        }
        else
        {
            $this->statusText = $statusText;
        }
    }
} 
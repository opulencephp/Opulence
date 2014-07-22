<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the response class for use in testing
 */
namespace RDev\Tests\Models\Web\Mocks;
use RDev\Models\Web;

class Response extends Web\Response
{
    /** @var array The list of header names to their properties */
    private $headers = [];
    /** @var array The list of cookie names to their properties */
    private $cookies = [];

    /**
     * @return array
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Sets a cookie in the response header
     *
     * @param string $name The name of the cookie
     * @param string $value The value of the cookie
     * @param \DateTime $expiration The expiration time as a Unix timestamp
     * @param string $path The path the cookie is valid on
     * @param string $domain The domain the cookie is valid on
     * @param bool $isSecure Whether or not this cookie should only be sent over SSL
     * @param bool $httpOnly Whether or not this cookie can be accessed exclusively over the HTTP protocol
     * @return bool True if successful, otherwise false
     */
    public function setCookie($name, $value, \DateTime $expiration, $path, $domain, $isSecure, $httpOnly)
    {
        $this->cookies[$name] = [
            "value" => $value,
            "expiration" => $expiration,
            "path" => $path,
            "domain" => $domain,
            "isSecure" => $isSecure,
            "httpOnly" => $httpOnly
        ];

        return true;
    }

    /**
     * Sets an HTTP response header
     *
     * @param string $name The name of the header to set
     * @param string $value The value of the header
     * @param bool $shouldReplace Whether or not this should replace a previous similar header
     * @param int|string $httpResponseCode The HTTP response code
     * @throws Web\Exceptions\WebException Thrown if the headers were already sent
     */
    public function setHeader($name, $value, $shouldReplace = true, $httpResponseCode = "")
    {
        $this->headers[$name] = [
            "value" => $value,
            "shouldReplace" => $shouldReplace,
            "httpResponseCode" => $httpResponseCode
        ];
    }

    /**
     * Sets the location header for a redirect
     *
     * @param string $url The URL to redirect to
     * @param bool $exitNow Whether or not we will exit immediately after sending the header
     * @throws Web\Exceptions\WebException Thrown if the headers were already sent
     */
    public function setLocation($url, $exitNow = true)
    {
        $this->setHeader("Location", $url);
    }
} 
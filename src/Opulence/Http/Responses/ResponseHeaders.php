<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Http\Responses;

use Opulence\Http\Headers;

/**
 * Defines the response headers
 */
class ResponseHeaders extends Headers
{
    /** HTML content type */
    const CONTENT_TYPE_HTML = 'text/html';
    /** JSON content type */
    const CONTENT_TYPE_JSON = 'application/json';
    /** Octet stream content type */
    const CONTENT_TYPE_OCTET_STREAM = 'application/octet-stream';
    /** PDF content type */
    const CONTENT_TYPE_PDF = 'application/pdf';
    /** Plain text content type */
    const CONTENT_TYPE_TEXT = 'text/plain';
    /** XML content type */
    const CONTENT_TYPE_XML = 'text/xml';
    /** Continue */
    const HTTP_CONTINUE = 100;
    /** Switching protocol */
    const HTTP_SWITCHING_PROTOCOL = 101;
    /** Successful response */
    const HTTP_OK = 200;
    /** Request has been fulfilled and a new resource has been created */
    const HTTP_CREATED = 201;
    /** The request has been accepted for processing, but processing hasn't completed */
    const HTTP_ACCEPTED = 202;
    /** The response was collected from a copy */
    const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    /** No content */
    const HTTP_NO_CONTENT = 204;
    /** After accomplishing request to tell user agent reset document view which sent the request */
    const HTTP_RESET_CONTENT = 205;
    /** The request contains partial content */
    const HTTP_PARTIAL_CONTENT = 206;
    /** Multiple choice redirect */
    const HTTP_MULTIPLE_CHOICE = 300;
    /** Moved permanently */
    const HTTP_MOVED_PERMANENTLY = 301;
    /** The URI has been changed temporarily */
    const HTTP_FOUND = 302;
    /** See other */
    const HTTP_SEE_OTHER = 303;
    /** The response has not been modified */
    const HTTP_NOT_MODIFIED = 304;
    /** The response must be accept by a proxy */
    const HTTP_USE_PROXY = 305;
    /** A temporary redirect */
    const HTTP_TEMPORARY_REDIRECT = 307;
    /** The request URI is now permanently at another URI */
    const HTTP_PERMANENT_REDIRECT = 308;
    /** The request was bad */
    const HTTP_BAD_REQUEST = 400;
    /** The request requires authentication */
    const HTTP_UNAUTHORIZED = 401;
    /** Payment is required */
    const HTTP_PAYMENT_REQUIRED = 402;
    /** The server understood the request, but is refusing to fulfill it */
    const HTTP_FORBIDDEN = 403;
    /** The server didn't find anything matching the request URI */
    const HTTP_NOT_FOUND = 404;
    /** The method is not allowed */
    const HTTP_METHOD_NOT_ALLOWED = 405;
    /** Cannot find content with the criteria from the user agent */
    const HTTP_NOT_ACCEPTABLE = 406;
    /** Authentication needs to be done via a proxy */
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    /** The request timed out */
    const HTTP_REQUEST_TIMEOUT = 408;
    /** There's a conflict with the state of the server */
    const HTTP_CONFLICT = 409;
    /** The content has been deleted from the server */
    const HTTP_GONE = 410;
    /** The The content-length header was required wasn't defined */
    const HTTP_LENGTH_REQUIRED = 411;
    /** Preconditions in the headers were not met */
    const HTTP_PRECONDITION_FAILED = 412;
    /** The request entity was too large */
    const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    /** The request media format wasn't supported */
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    /** The range header cannot be fulfilled */
    const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    /** The expected header cannot be met */
    const HTTP_EXPECTATION_FAILED = 417;
    /** The server encountered an unexpected condition which prevented it from fulfilling the request */
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    /** The server does not support the functionality required to fulfill the request */
    const HTTP_NOT_IMPLEMENTED = 501;
    /** The server acted as a gateway and got an invalid response */
    const HTTP_BAD_GATEWAY = 502;
    /** The server is currently unable to handle the request due to a temporary overloading/maintenance */
    const HTTP_SERVICE_UNAVAILABLE = 503;
    /** The server acted as a gateway and timed out */
    const HTTP_GATEWAY_TIMEOUT = 504;
    /** The HTTP version in the request isn't supported */
    const HTTP_HTTP_VERSION_NOT_SUPPORTED = 505;

    /** @var array Maps HTTP status codes to their default texts */
    public static $statusTexts = [
        self::HTTP_CONTINUE => 'Continue',
        self::HTTP_SWITCHING_PROTOCOL => 'Switching Protocol',
        self::HTTP_OK => 'OK',
        self::HTTP_CREATED => 'Created',
        self::HTTP_ACCEPTED => 'Accepted',
        self::HTTP_NON_AUTHORITATIVE_INFORMATION => 'Non-Authoritative Information',
        self::HTTP_NO_CONTENT => 'No Content',
        self::HTTP_RESET_CONTENT => 'Reset Content',
        self::HTTP_PARTIAL_CONTENT => 'Partial Content',
        self::HTTP_MULTIPLE_CHOICE => 'Multiple Choice',
        self::HTTP_MOVED_PERMANENTLY => 'Moved Permanently',
        self::HTTP_FOUND => 'Found',
        self::HTTP_SEE_OTHER => 'See Other',
        self::HTTP_NOT_MODIFIED => 'Not Modified',
        self::HTTP_USE_PROXY => 'Use Proxy',
        self::HTTP_TEMPORARY_REDIRECT => 'Temporary Redirect',
        self::HTTP_PERMANENT_REDIRECT => 'Permanent Redirect',
        self::HTTP_BAD_REQUEST => 'Bad Request',
        self::HTTP_UNAUTHORIZED => 'Unauthorized',
        self::HTTP_PAYMENT_REQUIRED => 'Payment Required',
        self::HTTP_FORBIDDEN => 'Forbidden',
        self::HTTP_NOT_FOUND => 'Not Found',
        self::HTTP_METHOD_NOT_ALLOWED => 'Method Not Allowed',
        self::HTTP_NOT_ACCEPTABLE => 'Not Acceptable',
        self::HTTP_PROXY_AUTHENTICATION_REQUIRED => 'Proxy Authentication Required',
        self::HTTP_REQUEST_TIMEOUT => 'Request Timeout',
        self::HTTP_CONFLICT => 'Conflict',
        self::HTTP_GONE => 'Gone',
        self::HTTP_LENGTH_REQUIRED => 'Length Required',
        self::HTTP_PRECONDITION_FAILED => 'Precondition Failed',
        self::HTTP_REQUEST_ENTITY_TOO_LARGE => 'Request Entity Too Large',
        self::HTTP_UNSUPPORTED_MEDIA_TYPE => 'Unsupported Media Type',
        self::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE => 'Requested Range Not Satisfiable',
        self::HTTP_EXPECTATION_FAILED => 'Expectation Failed',
        self::HTTP_INTERNAL_SERVER_ERROR => 'Internal Server Error',
        self::HTTP_NOT_IMPLEMENTED => 'Not Implemented',
        self::HTTP_BAD_GATEWAY => 'Bad Gateway',
        self::HTTP_SERVICE_UNAVAILABLE => 'Service Unavailable',
        self::HTTP_GATEWAY_TIMEOUT => 'Gateway Timeout',
        self::HTTP_HTTP_VERSION_NOT_SUPPORTED => 'HTTP Version Not Supported'
    ];
    /**
     * @var array The list of cookie names to their properties
     */
    private $cookies = [];

    /**
     * @param array $values The mapping of header names to values
     */
    public function __construct(array $values = [])
    {
        foreach ($values as $name => $value) {
            $name = strtoupper($name);
            $this->set(strtoupper($name), $value);
        }

        parent::__construct();
    }

    /**
     * Deletes a cookie in the response header
     *
     * @param string $name The name of the cookie to delete
     * @param string $path The path the cookie is valid on
     * @param string $domain The domain the cookie is valid on
     * @param bool $isSecure Whether or not the cookie was secure
     * @param bool $isHttpOnly Whether or not the cookie was HTTP-only
     */
    public function deleteCookie(
        string $name,
        string $path = '/',
        string $domain = '',
        bool $isSecure = false,
        bool $isHttpOnly = true
    ) {
        // Remove the cookie from the response
        $this->setCookie(new Cookie($name, '', 0, $path, $domain, $isSecure, $isHttpOnly));
    }

    /**
     * Gets a list of all the active cookies
     *
     * @param bool $includeDeletedCookies Whether or not to include deleted cookies
     * @return Cookie[] The list of all the set cookies
     */
    public function getCookies(bool $includeDeletedCookies = false) : array
    {
        $cookies = [];

        foreach ($this->cookies as $domain => $cookiesByDomain) {
            foreach ($cookiesByDomain as $path => $cookiesByPath) {
                /**
                 * @var string $name
                 * @var Cookie $cookie
                 */
                foreach ($cookiesByPath as $name => $cookie) {
                    // Only include active cookies
                    if ($includeDeletedCookies || $cookie->getExpiration() >= time()) {
                        $cookies[] = $cookie;
                    }
                }
            }
        }

        return $cookies;
    }

    /**
     * Sets a cookie
     *
     * @param Cookie $cookie The cookie to set
     */
    public function setCookie(Cookie $cookie)
    {
        $this->cookies[$cookie->getDomain()][$cookie->getPath()][$cookie->getName()] = $cookie;
    }

    /**
     * Sets multiple cookies
     *
     * @param Cookie[] $cookies
     */
    public function setCookies(array $cookies)
    {
        foreach ($cookies as $cookie) {
            $this->setCookie($cookie);
        }
    }
}

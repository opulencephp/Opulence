<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an HTTP response
 */
namespace RDev\Application\Shared\Models\Web;

class Response
{
    /** HTML content type */
    const CONTENT_TYPE_HTML = "text/html";
    /** JSON content type */
    const CONTENT_TYPE_JSON = "application/json";
    /** Octet stream content type */
    const CONTENT_TYPE_OCTET_STREAM = "application/octet-stream";
    /** PDF content type */
    const CONTENT_TYPE_PDF = "application/pdf";
    /** Plain text content type */
    const CONTENT_TYPE_TEXT = "text/plain";
    /** XML content type */
    const CONTENT_TYPE_XML = "text/xml";
    /** Successful response */
    const HTTP_OK = 200;
    /** Request has been fulfilled and a new resource has been created */
    const HTTP_CREATED = 201;
    /** The request has been accepted for processing, but processing hasn't completed */
    const HTTP_ACCEPTED = 202;
    /** The request was bad */
    const HTTP_BAD_REQUEST = 400;
    /** The request requires authentication */
    const HTTP_UNAUTHORIZED = 401;
    /** The server understood the request, but is refusing to fulfill it */
    const HTTP_FORBIDDEN = 403;
    /** The server didn't find anything matching the request URI */
    const HTTP_NOT_FOUND = 404;
    /** The server encountered an unexpected condition which prevented it from fulfilling the request */
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    /** The server does not support the functionality required to fulfill the request */
    const HTTP_NOT_IMPLEMENTED = 501;
    /** The server is currently unable to handle the request due to a temporary overloading/maintentance */
    const HTTP_SERVICE_UNAVAILABLE = 503;

    /**
     * Sets the location header for a redirect
     *
     * @param string $url The URL to redirect to
     * @param bool $exitNow Whether or not we will exit immediately after sending the header
     */
    public function setLocation($url, $exitNow = true)
    {
        header("Location: " . $url);

        if($exitNow)
        {
            exit;
        }
    }
} 
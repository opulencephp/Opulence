<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Contains data from an API response
 */
namespace RamODev\Application\Shared\Web\API;
use RamODev\Application\Shared\Web;

class Response
{
    /** @var int The HTTP response code */
    private $httpResponseCode = Web\Response::HTTP_OK;
    /** @var string The output of the request */
    private $output = "";
    /** @var string The content-type */
    private $contentType = "";

    /**
     * @param int $httpResponseCode The HTTP response code
     * @param string $output The output of the request
     * @param string $contentType The content-type
     */
    public function __construct($httpResponseCode, $output = "", $contentType = Web\Response::CONTENT_TYPE_TEXT)
    {
        $this->setHTTPResponseCode($httpResponseCode);
        $this->setOutput($output);
        $this->setContentType($contentType);
    }

    /**
     * @return int
     */
    public function getHTTPResponseCode()
    {
        return $this->httpResponseCode;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @param int $httpResponseCode
     */
    public function setHTTPResponseCode($httpResponseCode)
    {
        $this->httpResponseCode = $httpResponseCode;
    }

    /**
     * @param string $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }
} 
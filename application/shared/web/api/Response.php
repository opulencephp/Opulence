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

    /**
     * @param int $httpResponseCode The HTTP response code
     * @param string $output The output of the request
     */
    public function __construct($httpResponseCode, $output = "")
    {
        $this->setHTTPResponseCode($httpResponseCode);
        $this->setOutput($output);
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
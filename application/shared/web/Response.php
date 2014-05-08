<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an HTTP response
 */
namespace RamODev\Application\Shared\Web;

class Response
{
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
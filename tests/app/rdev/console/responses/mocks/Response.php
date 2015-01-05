<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Mocks the console response for use in tests
 */
namespace RDev\Tests\Console\Responses\Mocks;
use RDev\Console\Responses;

class Response extends Responses\Response
{
    /**
     * {@inheritdoc}
     */
    protected function doWrite($message, $includeNewLine)
    {
        echo $message . ($includeNewLine ? PHP_EOL : "");
    }
}
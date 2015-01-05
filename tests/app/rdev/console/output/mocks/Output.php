<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Mocks the console output for use in tests
 */
namespace RDev\Tests\Console\Output\Mocks;
use RDev\Console\Output as OutputNS;

class Output extends OutputNS\Output
{
    /**
     * {@inheritdoc}
     */
    protected function doWrite($message, $includeNewLine)
    {
        echo $message . ($includeNewLine ? PHP_EOL : "");
    }
}
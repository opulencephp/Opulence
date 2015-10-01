<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the console response for use in tests
 */
namespace Opulence\Tests\Console\Responses\Mocks;

use Opulence\Console\Responses\Response as BaseResponse;

class Response extends BaseResponse
{
    /**
     * Clears the response buffer
     */
    public function clear()
    {
        $this->write(chr(27) . "[2J" . chr(27) . "[;H");
    }

    /**
     * @inheritdoc
     */
    protected function doWrite($message, $includeNewLine)
    {
        echo $message . ($includeNewLine ? PHP_EOL : "");
    }
}
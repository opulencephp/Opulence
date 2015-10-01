<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the silent response, which does not write anything
 */
namespace Opulence\Console\Responses;

use Opulence\Console\Responses\Compilers\MockCompiler;

class SilentResponse extends Response
{
    public function __construct()
    {
        parent::__construct(new MockCompiler());
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        // Don't do anything
    }

    /**
     * @inheritdoc
     */
    public function write($messages)
    {
        // Don't do anything
    }

    /**
     * @inheritdoc
     */
    public function writeln($messages)
    {
        // Don't do anything
    }

    /**
     * @inheritdoc
     */
    protected function doWrite($message, $includeNewLine)
    {
        // Don't do anything
    }
}
<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a mock console compiler (useful for silent responses)
 */
namespace Opulence\Console\Responses\Compilers;

class MockCompiler implements ICompiler
{
    /**
     * @inheritdoc
     */
    public function compile($message)
    {
        return $message;
    }

    /**
     * @inheritdoc
     */
    public function setStyled($isStyled)
    {
        // Don't do anything
    }
}
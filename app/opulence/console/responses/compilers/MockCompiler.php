<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a mock console compiler (useful for silent responses)
 */
namespace Opulence\Console\Responses\Compilers;

use Opulence\Console\Responses\Compilers\Elements\Style;

class MockCompiler implements ICompiler
{
    /**
     * @inheritdoc
     */
    public function compile($message)
    {
        return $message;
    }

    public function registerElement($name, Style $style)
    {
        // Don't do anything
    }

    /**
     * @inheritdoc
     */
    public function setStyled($isStyled)
    {
        // Don't do anything
    }
}
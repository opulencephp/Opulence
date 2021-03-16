<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Console\Responses\Compilers;

use Opulence\Console\Responses\Compilers\Elements\Style;

/**
 * Defines a mock console compiler (useful for silent responses)
 */
class MockCompiler implements ICompiler
{
    /**
     * @inheritdoc
     */
    public function compile(string $message) : string
    {
        return $message;
    }

    public function registerElement(string $name, Style $style)
    {
        // Don't do anything
    }

    /**
     * @inheritdoc
     */
    public function setStyled(bool $isStyled)
    {
        // Don't do anything
    }
}

<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

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
    public function compile(string $message): string
    {
        return $message;
    }

    public function registerElement(string $name, Style $style): void
    {
        // Don't do anything
    }

    /**
     * @inheritdoc
     */
    public function setStyled(bool $isStyled): void
    {
        // Don't do anything
    }
}

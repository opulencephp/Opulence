<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Views\Tests\Compilers\Mocks;

use Opulence\Views\Compilers\Compiler as BaseCompiler;

/**
 * Mocks a compiler for use in testing
 */
class Compiler extends BaseCompiler
{
    /**
     * @inheritdoc
     * This mocks does not have any built-in view functions
     */
    public function __construct()
    {
        // Don't do anything
    }
}

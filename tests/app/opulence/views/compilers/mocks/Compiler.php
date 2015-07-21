<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a compiler for use in testing
 */
namespace Opulence\Tests\Views\Compilers\Mocks;
use Opulence\Views\Compilers\Compiler as BaseCompiler;

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
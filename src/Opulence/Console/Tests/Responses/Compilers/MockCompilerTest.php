<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Console\Tests\Responses\Compilers;

use Opulence\Console\Responses\Compilers\MockCompiler;

/**
 * Tests the mock compiler
 */
class MockCompilerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests compiling a styled message
     */
    public function testCompilingStyledMessage()
    {
        $compiler = new MockCompiler();
        $compiler->setStyled(true);
        $this->assertEquals('<foo>bar</foo>', $compiler->compile('<foo>bar</foo>'));
    }

    /**
     * Tests compiling an unstyled message
     */
    public function testCompilingUnstyledMessage()
    {
        $compiler = new MockCompiler();
        $compiler->setStyled(false);
        $this->assertEquals('<foo>bar</foo>', $compiler->compile('<foo>bar</foo>'));
    }
}

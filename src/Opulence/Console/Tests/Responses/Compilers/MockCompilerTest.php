<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Tests\Responses\Compilers;

use Opulence\Console\Responses\Compilers\MockCompiler;

/**
 * Tests the mock compiler
 */
class MockCompilerTest extends \PHPUnit\Framework\TestCase
{
    public function testCompilingStyledMessage(): void
    {
        $compiler = new MockCompiler();
        $compiler->setStyled(true);
        $this->assertEquals('<foo>bar</foo>', $compiler->compile('<foo>bar</foo>'));
    }

    public function testCompilingUnstyledMessage(): void
    {
        $compiler = new MockCompiler();
        $compiler->setStyled(false);
        $this->assertEquals('<foo>bar</foo>', $compiler->compile('<foo>bar</foo>'));
    }
}

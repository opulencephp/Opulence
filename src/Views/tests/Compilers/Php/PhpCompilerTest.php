<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\Tests\Compilers\Php;

use Opulence\Views\Compilers\Php\PhpCompiler;
use Opulence\Views\Compilers\ViewCompilerException;
use Opulence\Views\IView;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests the PHP compiler
 */
class PhpCompilerTest extends TestCase
{
    private PhpCompiler $compiler;

    protected function setUp(): void
    {
        $this->compiler = new PhpCompiler();
    }

    public function testExceptionsRethrownAsViewCompilerException(): void
    {
        $this->expectException(ViewCompilerException::class);
        $this->expectExceptionMessage('Exception thrown by view foo.php');
        /** @var IView|MockObject $view */
        $view = $this->createMock(IView::class);
        $view->method('getContents')
            ->willReturn('<?php ob_start();throw new \Exception("foo"); ?>');
        $view->method('getVars')
            ->willReturn([]);
        $view->method('getPath')
            ->willReturn('foo.php');
        $this->compiler->compile($view);
    }

    public function testOutputBufferLevelIsResetAfterException(): void
    {
        /** @var IView|MockObject $view */
        $view = $this->createMock(IView::class);
        $view->method('getContents')
            ->willReturn('<?php ob_start();throw new ' . ViewCompilerException::class . '("foo"); ?>');
        $view->method('getVars')
            ->willReturn([]);
        $obStartLevel = ob_get_level();

        try {
            $this->compiler->compile($view);
        } catch (ViewCompilerException $ex) {
            // Don't do anything
        }

        $this->assertEquals($obStartLevel, ob_get_level());
    }

    public function testThatVarsAreSet(): void
    {
        /** @var IView|MockObject $view */
        $view = $this->createMock(IView::class);
        $view->method('getContents')
            ->willReturn('<?php echo "$foo, $bar"; ?>');
        $view->method('getVars')
            ->willReturn(['foo' => 'Hello', 'bar' => 'world']);
        $this->assertEquals('Hello, world', $this->compiler->compile($view));
    }
}

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

use Exception;
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

    public function testExceptionsThrownAsThemselves(): void
    {
        $this->expectException(Exception::class);
        /** @var IView|MockObject $view */
        $view = $this->createMock(IView::class);
        $view->expects($this->any())
            ->method('getContents')
            ->willReturn('<?php ob_start();throw new \Exception("foo"); ?>');
        $view->expects($this->any())
            ->method('getVars')
            ->willReturn([]);
        $this->compiler->compile($view);
    }

    public function testOutputBufferLevelIsResetAfterException(): void
    {
        /** @var IView|MockObject $view */
        $view = $this->createMock(IView::class);
        $view->expects($this->any())
            ->method('getContents')
            ->willReturn('<?php ob_start();throw new ' . ViewCompilerException::class . '("foo"); ?>');
        $view->expects($this->any())
            ->method('getVars')
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
        $view->expects($this->any())
            ->method('getContents')
            ->willReturn('<?php echo "$foo, $bar"; ?>');
        $view->expects($this->any())
            ->method('getVars')
            ->willReturn(['foo' => 'Hello', 'bar' => 'world']);
        $this->assertEquals('Hello, world', $this->compiler->compile($view));
    }
}

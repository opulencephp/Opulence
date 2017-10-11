<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Views\Tests\Compilers\Php;

use Exception;
use Opulence\Views\Compilers\Php\PhpCompiler;
use Opulence\Views\Compilers\ViewCompilerException;
use Opulence\Views\IView;

/**
 * Tests the PHP compiler
 */
class PhpCompilerTest extends \PHPUnit\Framework\TestCase
{
    /** @var PhpCompiler The compiler to use in tests */
    private $compiler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->compiler = new PhpCompiler();
    }

    /**
     * Tests that any exception thrown as itself
     */
    public function testExceptionsThrownAsThemselves()
    {
        $this->expectException(Exception::class);
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $view */
        $view = $this->createMock(IView::class);
        $view->expects($this->any())
            ->method('getContents')
            ->willReturn('<?php ob_start();throw new \Exception("foo"); ?>');
        $view->expects($this->any())
            ->method('getVars')
            ->willReturn([]);
        $this->compiler->compile($view);
    }

    /**
     * Tests that the output buffer level is reset on exception
     */
    public function testOutputBufferLevelIsResetAfterException()
    {
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $view */
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

    /**
     * Tests that vars are set in the view
     */
    public function testThatVarsAreSet()
    {
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $view */
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

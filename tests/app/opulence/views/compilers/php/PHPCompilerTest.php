<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the PHP compiler
 */
namespace Opulence\Views\Compilers\PHP;

use Opulence\Views\Compilers\ViewCompilerException;
use Opulence\Views\IView;

class PHPCompilerTest extends \PHPUnit_Framework_TestCase
{
    /** @var PHPCompiler The compiler to use in tests */
    private $compiler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->compiler = new PHPCompiler();
    }

    /**
     * Tests that any exception is converted to correct exception
     */
    public function testExceptionsConvertedToCorrectType()
    {
        $this->setExpectedException(ViewCompilerException::class);
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $view */
        $view = $this->getMock(IView::class);
        $view->expects($this->any())
            ->method("getContents")
            ->willReturn('<?php ob_start();throw new \Exception("foo"); ?>');
        $view->expects($this->any())
            ->method("getVars")
            ->willReturn([]);
        $this->compiler->compile($view);
    }

    /**
     * Tests that the output buffer level is reset on exception
     */
    public function testOutputBufferLevelIsResetAfterException()
    {
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $view */
        $view = $this->getMock(IView::class);
        $view->expects($this->any())
            ->method("getContents")
            ->willReturn('<?php ob_start();throw new ' . ViewCompilerException::class . '("foo"); ?>');
        $view->expects($this->any())
            ->method("getVars")
            ->willReturn([]);
        $obStartLevel = ob_get_level();

        try {
            $this->compiler->compile($view);
        }catch (ViewCompilerException $ex) {
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
        $view = $this->getMock(IView::class);
        $view->expects($this->any())
            ->method("getContents")
            ->willReturn('<?php echo "$foo, $bar"; ?>');
        $view->expects($this->any())
            ->method("getVars")
            ->willReturn(["foo" => "Hello", "bar" => "world"]);
        $this->assertEquals('Hello, world', $this->compiler->compile($view));
    }
}
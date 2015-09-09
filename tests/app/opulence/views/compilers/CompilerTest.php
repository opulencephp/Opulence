<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the view compiler
 */
namespace Opulence\Views\Compilers;
use Opulence\Views\IView;

class CompilerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Compiler The compiler to use in tests */
    private $compiler = null;
    /** @var ICompilerRegistry|\PHPUnit_Framework_MockObject_MockObject The compiler registry to use in tests */
    private $registry = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->registry = $this->getMock(ICompilerRegistry::class);
        $this->compiler = new Compiler($this->registry);
    }

    /**
     * Tests that the correct compiler is used
     */
    public function testCorrectCompilerIsUsed()
    {
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $view */
        $view = $this->getMock(IView::class, [], [], "MockView");
        $view->expects($this->any())
            ->method("getContents")
            ->willReturn("foo");
        $view->expects($this->any())
            ->method("getVars")
            ->willReturn([]);
        /** @var ICompiler|\PHPUnit_Framework_MockObject_MockObject $compiler */
        $compiler = $this->getMock(ICompiler::class);
        $compiler->expects($this->once())
            ->method("compile")
            ->with($view)
            ->willReturn("bar");
        $this->registry->expects($this->once())
            ->method("get")
            ->with($view)
            ->willReturn($compiler);
        $this->registry->registerCompiler("MockView", $compiler);
        $this->compiler->compile($view);
    }
}
<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the compiler dispatcher
 */
namespace Opulence\Views\Compilers;
use InvalidArgumentException;
use Opulence\Views\IView;

class CompilerRegistryTest extends \PHPUnit_Framework_TestCase
{
    /** @var CompilerRegistry The registry to use in tests */
    private $registry = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->registry = new CompilerRegistry();
    }

    /**
     * Tests compiling a view that does not have a compiler
     */
    public function testCompilingViewThatDoesNotHaveCompiler()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $view */
        $view = $this->getMock(IView::class, [], [], "MockView");
        $this->registry->get($view);
    }

    /**
     * Tests registering a compiler
     */
    public function testRegisteringCompiler()
    {
        /** @var ICompiler|\PHPUnit_Framework_MockObject_MockObject $compiler */
        $compiler = $this->getMock(ICompiler::class);
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $view */
        $view = $this->getMock(IView::class, [], [], "MockView");
        $this->registry->registerCompiler("MockView", $compiler);
        $this->assertSame($compiler, $this->registry->get($view));
    }
}
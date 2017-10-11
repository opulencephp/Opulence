<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Views\Tests\Compilers;

use Opulence\Views\Compilers\Compiler;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Compilers\ICompilerRegistry;
use Opulence\Views\IView;

/**
 * Tests the view compiler
 */
class CompilerTest extends \PHPUnit\Framework\TestCase
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
        $this->registry = $this->createMock(ICompilerRegistry::class);
        $this->compiler = new Compiler($this->registry);
    }

    /**
     * Tests that the correct compiler is used
     */
    public function testCorrectCompilerIsUsed()
    {
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $view */
        $view = $this->getMockBuilder(IView::class)
            ->disableOriginalConstructor()
            ->setMockClassName('MockView')
            ->getMock();
        $view->expects($this->any())
            ->method('getContents')
            ->willReturn('foo');
        $view->expects($this->any())
            ->method('getVars')
            ->willReturn([]);
        /** @var ICompiler|\PHPUnit_Framework_MockObject_MockObject $compiler */
        $compiler = $this->createMock(ICompiler::class);
        $compiler->expects($this->once())
            ->method('compile')
            ->with($view)
            ->willReturn('bar');
        $this->registry->expects($this->once())
            ->method('getCompiler')
            ->with($view)
            ->willReturn($compiler);
        $this->registry->registerCompiler('MockView', $compiler);
        $this->compiler->compile($view);
    }
}

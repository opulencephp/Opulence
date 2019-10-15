<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\Tests\Compilers;

use Opulence\Views\Compilers\Compiler;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Compilers\ICompilerRegistry;
use Opulence\Views\IView;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests the view compiler
 */
class CompilerTest extends TestCase
{
    private Compiler $compiler;
    /** @var ICompilerRegistry|MockObject The compiler registry to use in tests */
    private ICompilerRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ICompilerRegistry::class);
        $this->compiler = new Compiler($this->registry);
    }

    public function testCorrectCompilerIsUsed(): void
    {
        /** @var IView|MockObject $view */
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
        /** @var ICompiler|MockObject $compiler */
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

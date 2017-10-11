<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Views\Tests\Compilers;

use InvalidArgumentException;
use Opulence\Views\Compilers\CompilerRegistry;
use Opulence\Views\IView;

/**
 * Tests the compiler dispatcher
 */
class CompilerRegistryTest extends \PHPUnit\Framework\TestCase
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
        $this->expectException(InvalidArgumentException::class);
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $view */
        $view = $this->createMock(IView::class);
        $view->expects($this->any())
            ->method('getPath')
            ->willReturn('foo');
        $this->registry->getCompiler($view);
    }

    /**
     * Tests registering a compiler
     */
    public function testRegisteringCompiler()
    {
        /** @var ICompiler|\PHPUnit_Framework_MockObject_MockObject $compiler */
        $compiler = $this->createMock(ICompiler::class);
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $view */
        $view = $this->createMock(IView::class);
        $view->expects($this->any())
            ->method('getPath')
            ->willReturn('php');
        $this->registry->registerCompiler('php', $compiler);
        $this->assertSame($compiler, $this->registry->getCompiler($view));
    }
}

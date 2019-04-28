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

use InvalidArgumentException;
use Opulence\Views\Compilers\CompilerRegistry;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\IView;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the compiler dispatcher
 */
class CompilerRegistryTest extends \PHPUnit\Framework\TestCase
{
    /** @var CompilerRegistry The registry to use in tests */
    private $registry;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->registry = new CompilerRegistry();
    }

    /**
     * Tests compiling a view that does not have a compiler
     */
    public function testCompilingViewThatDoesNotHaveCompiler(): void
    {
        $this->expectException(InvalidArgumentException::class);
        /** @var IView|MockObject $view */
        $view = $this->createMock(IView::class);
        $view->expects($this->any())
            ->method('getPath')
            ->willReturn('foo');
        $this->registry->getCompiler($view);
    }

    /**
     * Tests registering a compiler
     */
    public function testRegisteringCompiler(): void
    {
        /** @var ICompiler|MockObject $compiler */
        $compiler = $this->createMock(ICompiler::class);
        /** @var IView|MockObject $view */
        $view = $this->createMock(IView::class);
        $view->expects($this->any())
            ->method('getPath')
            ->willReturn('php');
        $this->registry->registerCompiler('php', $compiler);
        $this->assertSame($compiler, $this->registry->getCompiler($view));
    }
}

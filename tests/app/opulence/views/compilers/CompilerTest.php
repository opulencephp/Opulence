<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the view compiler
 */
namespace Opulence\Views\Compilers;
use Opulence\Views\Caching\ICache;
use Opulence\Views\IView;

class CompilerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Compiler The compiler to use in tests */
    private $compiler = null;
    /** @var ICompilerRegistry|\PHPUnit_Framework_MockObject_MockObject The compiler registry to use in tests */
    private $registry = null;
    /** @var ICache|\PHPUnit_Framework_MockObject_MockObject The cache to use in tests */
    private $cache = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->registry = $this->getMock(ICompilerRegistry::class);
        $this->cache = $this->getMock(ICache::class);
        $this->compiler = new Compiler($this->registry, $this->cache);
    }

    /**
     * Tests that cached views are returned if they're found
     */
    public function testCachedViewsAreReturnedIfFound()
    {
        $this->cache->expects($this->any())
            ->method("has")
            ->willReturn(true);
        $this->cache->expects($this->any())
            ->method("get")
            ->willReturn("foo");
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $view */
        $view = $this->getMock(IView::class);
        $view->expects($this->any())
            ->method("getVars")
            ->willReturn([]);
        $this->assertEquals("foo", $this->compiler->compile($view));
    }

    /**
     * Tests that the compiler is called when the view is not found in cache
     */
    public function testCompilerIsCalledWhenViewIsNotFoundInCache()
    {
        $this->cache->expects($this->any())
            ->method("has")
            ->willReturn(false);
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $view */
        $view = $this->getMock(IView::class, [], [], "MockView");
        $view->expects($this->once())
            ->method("getVars")
            ->willReturn([]);
        /** @var ICompiler|\PHPUnit_Framework_MockObject_MockObject $compiler */
        $compiler = $this->getMock(ICompiler::class);
        $compiler->expects($this->once())
            ->method("compile")
            ->with($view, "foo");
        $this->registry->expects($this->once())
            ->method("get")
            ->with($view)
            ->willReturn($compiler);
        $this->registry->registerCompiler("MockView", $compiler);
        $this->compiler->compile($view, "foo");
    }

    /**
     * Tests not passing contents to compile
     */
    public function testNotPassingContentsToCompile()
    {
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $view */
        $view = $this->getMock(IView::class, [], [], "MockView");
        $view->expects($this->once())
            ->method("getContents")
            ->willReturn("bar");
        $view->expects($this->any())
            ->method("getVars")
            ->willReturn([]);
        /** @var ICompiler|\PHPUnit_Framework_MockObject_MockObject $compiler */
        $compiler = $this->getMock(ICompiler::class);
        $compiler->expects($this->once())
            ->method("compile")
            ->with($view, "bar");
        $this->registry->expects($this->once())
            ->method("get")
            ->with($view)
            ->willReturn($compiler);
        $this->registry->registerCompiler("MockView", $compiler);
        $this->compiler->compile($view);
    }

    /**
     * Tests passing contents to compile
     */
    public function testPassingContentsToCompile()
    {
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $view */
        $view = $this->getMock(IView::class, [], [], "MockView");
        $view->expects($this->never())
            ->method("getContents");
        $view->expects($this->any())
            ->method("getVars")
            ->willReturn([]);
        /** @var ICompiler|\PHPUnit_Framework_MockObject_MockObject $compiler */
        $compiler = $this->getMock(ICompiler::class);
        $this->registry->expects($this->once())
            ->method("get")
            ->with($view)
            ->willReturn($compiler);
        $this->registry->registerCompiler("MockView", $compiler);
        $this->compiler->compile($view, "foo");
    }
}
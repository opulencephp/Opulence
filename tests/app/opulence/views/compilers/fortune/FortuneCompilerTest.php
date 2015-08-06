<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the Fortune compiler
 */
namespace Opulence\Views\Compilers\Fortune;
use Opulence\Views\Caching\ICache;
use Opulence\Views\Compilers\Compiler;
use Opulence\Views\Compilers\Fortune\Lexers\Lexer;
use Opulence\Views\Compilers\Fortune\Parsers\Parser;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Compilers\ICompilerRegistry;
use Opulence\Views\Factories\IViewFactory;
use Opulence\Views\Filters\XSSFilter;
use Opulence\Views\View;

class FortuneCompilerTest extends \PHPUnit_Framework_TestCase
{
    /** @var FortuneCompiler The compiler to use in tests */
    private $fortuneCompiler = null;
    /** @var Transpiler The transpiler to use in tests */
    private $transpiler = null;
    /** @var ICompiler|\PHPUnit_Framework_MockObject_MockObject The main view compiler to use in tests */
    private $mainCompiler = null;
    /** @var IViewFactory|\PHPUnit_Framework_MockObject_MockObject The view factory to use in tests */
    private $viewFactory = null;
    /** @var View The view to use in tests */
    private $view = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->transpiler = new Transpiler(new Lexer(), new Parser(), new XSSFilter());
        /** @var ICompilerRegistry|\PHPUnit_Framework_MockObject_MockObject $registry */
        $registry = $this->getMock(ICompilerRegistry::class);
        /** @var ICache|\PHPUnit_Framework_MockObject_MockObject $cache */
        $cache = $this->getMock(ICache::class);
        $cache->expects($this->any())
            ->method("has")
            ->willReturn(false);
        $this->mainCompiler = new Compiler($registry, $cache);
        $this->viewFactory = $this->getMock(IViewFactory::class);
        $this->fortuneCompiler = new FortuneCompiler($this->transpiler, $this->mainCompiler, $this->viewFactory);
        $this->view = new View();
        // Need to make sure we always return the Fortune compiler
        $registry->expects($this->any())
            ->method("get")
            ->willReturn($this->fortuneCompiler);
    }

    /**
     * Tests compiling an else-if statement
     */
    public function testCompilingElseIfStatement()
    {
        $this->assertEquals(
            "",
            $this->fortuneCompiler->compile($this->view, '<% if(false) %>foo<% elseif(false) %>bar<% endif %>')
        );
        $this->assertEquals(
            "bar",
            $this->fortuneCompiler->compile($this->view, '<% if(false) %>foo<% elseif(true) %>bar<% endif %>')
        );
    }

    /**
     * Tests compiling an else statement
     */
    public function testCompilingElseStatement()
    {
        $this->assertEquals(
            "bar",
            $this->fortuneCompiler->compile($this->view, '<% if(false) %>foo<% else %>bar<% endif %>')
        );
    }

    /**
     * Tests compiling a for-else loop
     */
    public function testCompilingForElseLoop()
    {
        $this->assertEquals(
            "01",
            $this->fortuneCompiler->compile($this->view, '<% forelse([0, 1] as $item) %>{{$item}}<% elseifempty %>empty<% endif %>')
        );
        $this->assertEquals(
            "empty",
            $this->fortuneCompiler->compile($this->view, '<% forelse([] as $item) %>{{$item}}<% elseifempty %>empty<% endif %>')
        );
    }

    /**
     * Tests compiling a for loop
     */
    public function testCompilingForLoop()
    {
        $this->assertEquals(
            "01",
            $this->fortuneCompiler->compile($this->view, '<% for($i=0;$i<2;$i++) %>{{$i}}<% endfor %>')
        );
    }

    /**
     * Tests compiling a foreach loop
     */
    public function testCompilingForeachLoop()
    {
        $this->assertEquals(
            "01",
            $this->fortuneCompiler->compile($this->view, '<% foreach([0, 1] as $item) %>{{$item}}<% endforeach %>')
        );
    }

    /**
     * Tests compiling an if statement
     */
    public function testCompilingIfStatement()
    {
        $this->assertEquals(
            "",
            $this->fortuneCompiler->compile($this->view, '<% if(false) %>foo<% endif %>')
        );
        $this->assertEquals(
            "foo",
            $this->fortuneCompiler->compile($this->view, '<% if(true) %>foo<% endif %>')
        );
    }

    /**
     * Tests compiling an include statement
     */
    public function testCompilingIncludeStatement()
    {
        $includedView = new View("Foo", "foo");
        $this->viewFactory->expects($this->once())
            ->method("create")
            ->willReturn($includedView);
        $this->assertEquals(
            "foobar",
            $this->fortuneCompiler->compile($this->view, '<% include("Foo") %>bar')
        );
    }

    /**
     * Tests compiling nested for-else loops
     */
    public function testCompilingNestedForElseLoops()
    {
        $this->assertEquals(
            "02031213",
            $this->fortuneCompiler->compile($this->view, '<% forelse([0, 1] as $x) %><% forelse([2, 3] as $y) %>{{$x}}{{$y}}<% elseifempty %>empty2<% endif %><% elseifempty %>empty1<% endif %>')
        );
        $this->assertEquals(
            "empty1",
            $this->fortuneCompiler->compile($this->view, '<% forelse([] as $x) %><% forelse([] as $y) %>{{$x}}{{$y}}<% elseifempty %>empty2<% endif %><% elseifempty %>empty1<% endif %>')
        );
        $this->assertEquals(
            "empty2empty2",
            $this->fortuneCompiler->compile($this->view, '<% forelse([0, 1] as $x) %><% forelse([] as $y) %>{{$x}}{{$y}}<% elseifempty %>empty2<% endif %><% elseifempty %>empty1<% endif %>')
        );
    }

    /**
     * Tests compiling nested parent statements
     */
    public function testCompilingNestedParentStatements()
    {
        $parent1 = new View("Foo", '<% part("foo") %>bar<% endpart %><% show("foo") %>');
        $parent2 = new View("Bar", '<% extends("Foo") %><% part("foo") %><% parent %>baz<% endpart %>');
        $this->viewFactory->expects($this->at(0))
            ->method("create")
            ->with("Bar")
            ->willReturn($parent2);
        $this->viewFactory->expects($this->at(1))
            ->method("create")
            ->with("Foo")
            ->willReturn($parent1);
        $this->assertEquals(
            "barbazblah",
            $this->fortuneCompiler->compile($this->view, '<% extends("Bar") %><% part("foo") %><% parent %>blah<% endpart %>')
        );
    }

    /**
     * Tests compiling a parent statement
     */
    public function testCompilingParentStatement()
    {
        $includedView = new View("Foo", '<% part("foo") %>bar<% endpart %><% show("foo") %>');
        $this->viewFactory->expects($this->once())
            ->method("create")
            ->with("Foo")
            ->willReturn($includedView);
        $this->assertEquals(
            "barbaz",
            $this->fortuneCompiler->compile($this->view, '<% extends("Foo") %><% part("foo") %><% parent %>baz<% endpart %>')
        );
    }

    /**
     * Tests compiling part and show statements
     */
    public function testCompilingPartAndShowStatements()
    {
        $this->assertEquals(
            "",
            $this->fortuneCompiler->compile($this->view, '<% part("a") %>foo<% endpart %>')
        );
        $this->assertEquals(
            "foo",
            $this->fortuneCompiler->compile($this->view, '<% part("a") %>foo<% endpart %><% show("a") %>')
        );
    }

    /**
     * Tests compiling a while loop
     */
    public function testCompilingWhileLoop()
    {
        $this->assertEquals(
            "01",
            $this->fortuneCompiler->compile($this->view, '<?php $i = 0; ?><% while($i < 2) %>{{$i}}<?php $i++; ?><% endwhile %>')
        );
    }

    /**
     * Tests overwriting a parent's part
     */
    public function testOverwritingParentPart()
    {
        $includedView = new View("Foo", '<% part("foo") %>bar<% endpart %><% show("foo") %>');
        $this->viewFactory->expects($this->once())
            ->method("create")
            ->with("Foo")
            ->willReturn($includedView);
        $this->assertEquals(
            "baz",
            $this->fortuneCompiler->compile($this->view, '<% extends("Foo") %><% part("foo") %>baz<% endpart %>')
        );
    }

    /**
     * Tests that the transpiler is called
     */
    public function testTranspilerIsCalled()
    {
        /** @var ITranspiler|\PHPUnit_Framework_MockObject_MockObject $transpiler */
        $transpiler = $this->getMock(ITranspiler::class);
        $transpiler->expects($this->once())
            ->method("transpile")
            ->with($this->view, "foo")
            ->willReturn('<?php echo "bar"; ?>');
        /** @var IViewFactory|\PHPUnit_Framework_MockObject_MockObject $viewFactory */
        $viewFactory = $this->getMock(IViewFactory::class);
        $compiler = new FortuneCompiler($transpiler, $this->mainCompiler, $viewFactory);
        $this->assertEquals("bar", $compiler->compile($this->view, "foo"));
        $this->assertSame($this->mainCompiler, $this->view->getVar("__opulenceViewCompiler"));
        $this->assertSame($viewFactory, $this->view->getVar("__opulenceViewFactory"));
        $this->assertSame($transpiler, $this->view->getVar("__opulenceFortuneTranspiler"));
    }
}
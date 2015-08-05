<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the Fortune compiler
 */
namespace Opulence\Views\Compilers\Fortune;
use Opulence\Views\Compilers\Fortune\Lexers\Lexer;
use Opulence\Views\Compilers\Fortune\Parsers\Parser;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Factories\IViewFactory;
use Opulence\Views\Filters\XSSFilter;
use Opulence\Views\View;

class FortuneCompilerTest extends \PHPUnit_Framework_TestCase
{
    /** @var FortuneCompiler The compiler to use in tests */
    private $compiler = null;
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
        $this->mainCompiler = $this->getMock(ICompiler::class);
        $this->viewFactory = $this->getMock(IViewFactory::class);
        $this->compiler = new FortuneCompiler($this->transpiler, $this->mainCompiler, $this->viewFactory);
        $this->view = new View();
    }

    /**
     * Tests compiling an else-if statement
     */
    public function testCompilingElseIfStatement()
    {
        $this->assertEquals(
            "",
            $this->compiler->compile($this->view, '<% if(false) %>foo<% elseif(false) %>bar<% endif %>')
        );
        $this->assertEquals(
            "bar",
            $this->compiler->compile($this->view, '<% if(false) %>foo<% elseif(true) %>bar<% endif %>')
        );
    }

    /**
     * Tests compiling an else statement
     */
    public function testCompilingElseStatement()
    {
        $this->assertEquals(
            "bar",
            $this->compiler->compile($this->view, '<% if(false) %>foo<% else %>bar<% endif %>')
        );
    }

    /**
     * Tests compiling a for-else loop
     */
    public function testCompilingForElseLoop()
    {
        $this->assertEquals(
            "01",
            $this->compiler->compile($this->view, '<% forelse([0, 1] as $item) %>{{$item}}<% elseifempty %>empty<% endif %>')
        );
        $this->assertEquals(
            "empty",
            $this->compiler->compile($this->view, '<% forelse([] as $item) %>{{$item}}<% elseifempty %>empty<% endif %>')
        );
    }

    /**
     * Tests compiling a for loop
     */
    public function testCompilingForLoop()
    {
        $this->assertEquals(
            "01",
            $this->compiler->compile($this->view, '<% for($i=0;$i<2;$i++) %>{{$i}}<% endfor %>')
        );
    }

    /**
     * Tests compiling a foreach loop
     */
    public function testCompilingForeachLoop()
    {
        $this->assertEquals(
            "01",
            $this->compiler->compile($this->view, '<% foreach([0, 1] as $item) %>{{$item}}<% endforeach %>')
        );
    }

    /**
     * Tests compiling an if statement
     */
    public function testCompilingIfStatement()
    {
        $this->assertEquals(
            "",
            $this->compiler->compile($this->view, '<% if(false) %>foo<% endif %>')
        );
        $this->assertEquals(
            "foo",
            $this->compiler->compile($this->view, '<% if(true) %>foo<% endif %>')
        );
    }

    /**
     * Tests compiling nested for-else loops
     */
    public function testCompilingNestedForElseLoops()
    {
        $this->assertEquals(
            "02031213",
            $this->compiler->compile($this->view, '<% forelse([0, 1] as $x) %><% forelse([2, 3] as $y) %>{{$x}}{{$y}}<% elseifempty %>empty2<% endif %><% elseifempty %>empty1<% endif %>')
        );
        $this->assertEquals(
            "empty1",
            $this->compiler->compile($this->view, '<% forelse([] as $x) %><% forelse([] as $y) %>{{$x}}{{$y}}<% elseifempty %>empty2<% endif %><% elseifempty %>empty1<% endif %>')
        );
        $this->assertEquals(
            "empty2empty2",
            $this->compiler->compile($this->view, '<% forelse([0, 1] as $x) %><% forelse([] as $y) %>{{$x}}{{$y}}<% elseifempty %>empty2<% endif %><% elseifempty %>empty1<% endif %>')
        );
    }

    /**
     * Tests compiling part and show statements
     */
    public function testCompilingPartAndShowStatements()
    {
        $this->assertEquals(
            "",
            $this->compiler->compile($this->view, '<% part("a") %>foo<% endpart %>')
        );
        $this->assertEquals(
            "foo",
            $this->compiler->compile($this->view, '<% part("a") %>foo<% endpart %><% show("a") %>')
        );
    }

    /**
     * Tests compiling a while loop
     */
    public function testCompilingWhileLoop()
    {
        $this->assertEquals(
            "01",
            $this->compiler->compile($this->view, '<?php $i = 0; ?><% while($i < 2) %>{{$i}}<?php $i++; ?><% endwhile %>')
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
        $compiler = new FortuneCompiler($transpiler, $this->mainCompiler, $this->viewFactory);
        $this->assertEquals("bar", $compiler->compile($this->view, "foo"));
        $this->assertEquals($this->mainCompiler, $this->view->getVar("__opulenceViewCompiler"));
        $this->assertEquals($this->viewFactory, $this->view->getVar("__opulenceViewFactory"));
        $this->assertEquals($transpiler, $this->view->getVar("__opulenceFortuneTranspiler"));
    }
}
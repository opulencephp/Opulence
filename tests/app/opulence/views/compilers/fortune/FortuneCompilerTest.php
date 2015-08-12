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
use Opulence\Views\IView;
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
     * Tests that a child inherits parent's variables
     */
    public function testChildInheritsParentsVariables()
    {
        $parentView = new View("Foo", "");
        $parentView->setVar("foo", "bar");
        $this->viewFactory->expects($this->once())
            ->method("create")
            ->with("Foo")
            ->willReturn($parentView);
        $this->assertEquals(
            "bar",
            $this->fortuneCompiler->compile($this->view, '<% extends("Foo") %>{{$foo}}')
        );
    }

    /**
     * Tests compiling a custom view function
     */
    public function testCompilingCustomViewFunction()
    {
        $this->transpiler->registerViewFunction("foo", function ($input)
        {
            return "foo: $input";
        });
        $this->assertEquals("foo: bar", $this->fortuneCompiler->compile($this->view, '{{!foo("bar")!}}'));
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
     * Tests compiling escaped structures
     */
    public function testCompilingEscapedStructures()
    {
        $this->assertEquals(
            "{{foo}}{{!bar!}}<% baz %>",
            $this->fortuneCompiler->compile($this->view, '\{{foo}}\{{!bar!}}\<% baz %>')
        );
    }

    /**
     * Tests compiling a for-if loop
     */
    public function testCompilingForIfLoop()
    {
        $this->assertEquals(
            "01",
            $this->fortuneCompiler->compile($this->view, '<% forif([0, 1] as $item) %>{{$item}}<% forelse %>empty<% endif %>')
        );
        $this->assertEquals(
            "empty",
            $this->fortuneCompiler->compile($this->view, '<% forif([] as $item) %>{{$item}}<% forelse %>empty<% endif %>')
        );
    }

    /**
     * Tests compiling a for loop
     */
    public function testCompilingForLoop()
    {
        $this->assertEquals(
            "01",
            $this->fortuneCompiler->compile($this->view, '<% for($i=0;$i<2;$i++) %><?php echo $i; ?><% endfor %>')
        );
    }

    /**
     * Tests compiling a foreach loop
     */
    public function testCompilingForeachLoop()
    {
        $this->assertEquals(
            "01",
            $this->fortuneCompiler->compile($this->view, '<% foreach([0, 1] as $item) %><?php echo $item; ?><% endforeach %>')
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
     * Tests compiling nested for-if loops
     */
    public function testCompilingNestedForIfLoops()
    {
        $this->assertEquals(
            "02031213",
            $this->fortuneCompiler->compile($this->view, '<% forif([0, 1] as $x) %><% forif([2, 3] as $y) %>{{$x}}{{$y}}<% forelse %>empty2<% endif %><% forelse %>empty1<% endif %>')
        );
        $this->assertEquals(
            "empty1",
            $this->fortuneCompiler->compile($this->view, '<% forif([] as $x) %><% forif([] as $y) %>{{$x}}{{$y}}<% forelse %>empty2<% endif %><% forelse %>empty1<% endif %>')
        );
        $this->assertEquals(
            "empty2empty2",
            $this->fortuneCompiler->compile($this->view, '<% forif([0, 1] as $x) %><% forif([] as $y) %>{{$x}}{{$y}}<% forelse %>empty2<% endif %><% forelse %>empty1<% endif %>')
        );
    }

    /**
     * Tests compiling nested include statements
     */
    public function testCompilingNestedIncludeStatements()
    {
        $includedView1 = new View("Foo", 'foo<% include("Bar") %>');
        $includedView2 = new View("Bar", 'bar');
        $this->viewFactory->expects($this->at(0))
            ->method("create")
            ->willReturn($includedView1);
        $this->viewFactory->expects($this->at(1))
            ->method("create")
            ->willReturn($includedView2);
        $this->assertEquals(
            "foobarbaz",
            $this->fortuneCompiler->compile($this->view, '<% include("Foo") %>baz')
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
        $parentView = new View("Foo", '<% part("foo") %>bar<% endpart %><% show("foo") %>');
        $this->viewFactory->expects($this->once())
            ->method("create")
            ->with("Foo")
            ->willReturn($parentView);
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
     * Tests compiling a template that uses custom tag delimiters
     */
    public function testCompilingTemplateWithCustomTags()
    {
        $this->view->setContents('^^"A&W"$$ ++"A&W"-- (* if(true) *)foo(* endif *)');
        $this->view->setDelimiters(IView::DELIMITER_TYPE_UNSANITIZED_TAG, ["^^", "$$"]);
        $this->view->setDelimiters(IView::DELIMITER_TYPE_SANITIZED_TAG, ["++", "--"]);
        $this->view->setDelimiters(IView::DELIMITER_TYPE_DIRECTIVE, ["(*", "*)"]);
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "A&W A&amp;W foo",
                $this->fortuneCompiler->compile($this->view, $this->view->getContents())
            )
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
     * Tests overriding a grandparent's part
     */
    public function testOverridingGrandparentPart()
    {
        $grandparentView = new View("Foo", '<% part("foo") %>bar<% endpart %><% show("foo") %>');
        $parentView = new View("Foo", '<% extends("Grandparent") %><% part("foo") %>baz<% endpart %>');
        $this->viewFactory->expects($this->at(0))
            ->method("create")
            ->with("Parent")
            ->willReturn($parentView);
        $this->viewFactory->expects($this->at(1))
            ->method("create")
            ->with("Grandparent")
            ->willReturn($grandparentView);
        $this->assertEquals(
            "blah",
            $this->fortuneCompiler->compile($this->view, '<% extends("Parent") %><% part("foo") %>blah<% endpart %>')
        );
    }

    /**
     * Tests overriding a grandparent's variable
     */
    public function testOverridingGrandparentVariable()
    {
        $grandparentView = new View("Foo", '{{$foo}}');
        $grandparentView->setVar("foo", "bar");
        $parentView = new View("Foo", '<% extends("Grandparent") %>');
        $this->viewFactory->expects($this->at(0))
            ->method("create")
            ->with("Parent")
            ->willReturn($parentView);
        $this->viewFactory->expects($this->at(1))
            ->method("create")
            ->with("Grandparent")
            ->willReturn($grandparentView);
        $this->view->setVar("foo", "baz");
        $this->assertEquals(
            "baz",
            $this->fortuneCompiler->compile($this->view, '<% extends("Parent") %>')
        );
    }

    /**
     * Tests overriding a parent's variable
     */
    public function testOverridingParentVariable()
    {
        $parentView = new View("Foo", '{{$foo}}');
        $parentView->setVar("foo", "bar");
        $this->viewFactory->expects($this->once())
            ->method("create")
            ->with("Foo")
            ->willReturn($parentView);
        $this->view->setVar("foo", "baz");
        $this->assertEquals(
            "baz",
            $this->fortuneCompiler->compile($this->view, '<% extends("Foo") %>')
        );
    }

    /**
     * Tests overriding a parent's part
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

    /**
     * Checks if two strings with encoded characters are equal
     * This is necessary because, for example, HHVM encodes "&" to "&#38;" whereas PHP 5.6 encodes to "&amp;"
     * This method makes those two alternate characters equivalent
     *
     * @param string $string1 The first string to compare
     * @param string $string2 The second string to compare
     * @return bool True if the strings are equal, otherwise false
     */
    protected function stringsWithEncodedCharactersEqual($string1, $string2)
    {
        // Convert ampersand
        $string1 = str_replace("&#38;", "&amp;", $string1);
        $string2 = str_replace("&#38;", "&amp;", $string2);
        // Convert single quote
        $string1 = str_replace("&#039", "&#39", $string1);
        $string2 = str_replace("&#039", "&#39", $string2);
        // Convert double quotes
        $string1 = str_replace("&quot;", "&#34;", $string1);
        $string2 = str_replace("&quot;", "&#34;", $string2);
        if($string1 === $string2)
        {
            return true;
        }
        else
        {
            error_log($string1 . "::" . $string2);

            return false;
        }
    }
}
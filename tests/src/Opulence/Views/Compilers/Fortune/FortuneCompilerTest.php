<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Views\Compilers\Fortune;

use Opulence\Views\Caching\ICache;
use Opulence\Views\Compilers\Fortune\Lexers\Lexer;
use Opulence\Views\Compilers\Fortune\Parsers\Parser;
use Opulence\Views\Compilers\ICompilerRegistry;
use Opulence\Views\Factories\IViewFactory;
use Opulence\Views\Filters\XssFilter;
use Opulence\Views\IView;
use Opulence\Views\View;

/**
 * Tests the Fortune compiler
 */
class FortuneCompilerTest extends \PHPUnit\Framework\TestCase
{
    /** @var FortuneCompiler The compiler to use in tests */
    private $fortuneCompiler = null;
    /** @var Transpiler The transpiler to use in tests */
    private $transpiler = null;
    /** @var IViewFactory|\PHPUnit_Framework_MockObject_MockObject The view factory to use in tests */
    private $viewFactory = null;
    /** @var View The view to use in tests */
    private $view = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        /** @var ICompilerRegistry|\PHPUnit_Framework_MockObject_MockObject $registry */
        $registry = $this->createMock(ICompilerRegistry::class);
        /** @var ICache|\PHPUnit_Framework_MockObject_MockObject $cache */
        $cache = $this->createMock(ICache::class);
        $cache->expects($this->any())
            ->method("has")
            ->willReturn(false);
        $this->transpiler = new Transpiler(new Lexer(), new Parser(), $cache, new XssFilter());
        $this->viewFactory = $this->createMock(IViewFactory::class);
        $this->fortuneCompiler = new FortuneCompiler($this->transpiler, $this->viewFactory);
        $this->view = new View();
        // Need to make sure we always return the Fortune compiler
        $registry->expects($this->any())
            ->method("getCompiler")
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
            ->method("createView")
            ->with("Foo")
            ->willReturn($parentView);
        $this->view->setContents('<% extends("Foo") %>{{$foo}}');
        $this->assertEquals(
            "bar",
            $this->fortuneCompiler->compile($this->view)
        );
    }

    /**
     * Tests compiling a custom view function
     */
    public function testCompilingCustomViewFunction()
    {
        $this->transpiler->registerViewFunction("foo", function ($input) {
            return "foo: $input";
        });
        $this->view->setContents('{{!foo("bar")!}}');
        $this->assertEquals("foo: bar", $this->fortuneCompiler->compile($this->view));
    }

    /**
     * Tests compiling an else-if statement
     */
    public function testCompilingElseIfStatement()
    {
        $this->view->setContents('<% if(false) %>foo<% elseif(false) %>bar<% endif %>');
        $this->assertEquals(
            "",
            $this->fortuneCompiler->compile($this->view)
        );
        $this->view->setContents('<% if(false) %>foo<% elseif(true) %>bar<% endif %>');
        $this->assertEquals(
            "bar",
            $this->fortuneCompiler->compile($this->view)
        );
    }

    /**
     * Tests compiling an else statement
     */
    public function testCompilingElseStatement()
    {
        $this->view->setContents('<% if(false) %>foo<% else %>bar<% endif %>');
        $this->assertEquals(
            "bar",
            $this->fortuneCompiler->compile($this->view)
        );
    }

    /**
     * Tests compiling escaped structures
     */
    public function testCompilingEscapedStructures()
    {
        $this->view->setContents('\{{foo}}\{{!bar!}}\<% baz %>');
        $this->assertEquals(
            "{{foo}}{{!bar!}}<% baz %>",
            $this->fortuneCompiler->compile($this->view)
        );
    }

    /**
     * Tests compiling a for-if loop
     */
    public function testCompilingForIfLoop()
    {
        $this->view->setContents('<% forif([0, 1] as $item) %>{{$item}}<% forelse %>empty<% endif %>');
        $this->assertEquals(
            "01",
            $this->fortuneCompiler->compile($this->view)
        );
        $this->view->setContents('<% forif([] as $item) %>{{$item}}<% forelse %>empty<% endif %>');
        $this->assertEquals(
            "empty",
            $this->fortuneCompiler->compile($this->view)
        );
    }

    /**
     * Tests compiling a for loop
     */
    public function testCompilingForLoop()
    {
        $this->view->setContents('<% for($i=0;$i<2;$i++) %><?php echo $i; ?><% endfor %>');
        $this->assertEquals(
            "01",
            $this->fortuneCompiler->compile($this->view)
        );
    }

    /**
     * Tests compiling a foreach loop
     */
    public function testCompilingForeachLoop()
    {
        $this->view->setContents('<% foreach([0, 1] as $item) %><?php echo $item; ?><% endforeach %>');
        $this->assertEquals(
            "01",
            $this->fortuneCompiler->compile($this->view)
        );
    }

    /**
     * Tests compiling an if statement
     */
    public function testCompilingIfStatement()
    {
        $this->view->setContents('<% if(false) %>foo<% endif %>');
        $this->assertEquals(
            "",
            $this->fortuneCompiler->compile($this->view)
        );
        $this->view->setContents('<% if(true) %>foo<% endif %>');
        $this->assertEquals(
            "foo",
            $this->fortuneCompiler->compile($this->view)
        );
    }

    /**
     * Tests compiling an include statement
     */
    public function testCompilingIncludeStatement()
    {
        $includedView = new View("Foo", "foo");
        $this->viewFactory->expects($this->once())
            ->method("createView")
            ->willReturn($includedView);
        $this->view->setContents('<% include("Foo") %>bar');
        $this->assertEquals(
            "foobar",
            $this->fortuneCompiler->compile($this->view)
        );
    }

    /**
     * Tests compiling nested for-if loops
     */
    public function testCompilingNestedForIfLoops()
    {
        $this->view->setContents('<% forif([0, 1] as $x) %><% forif([2, 3] as $y) %>{{$x}}{{$y}}<% forelse %>empty2<% endif %><% forelse %>empty1<% endif %>');
        $this->assertEquals(
            "02031213",
            $this->fortuneCompiler->compile($this->view)
        );
        $this->view->setContents('<% forif([] as $x) %><% forif([] as $y) %>{{$x}}{{$y}}<% forelse %>empty2<% endif %><% forelse %>empty1<% endif %>');
        $this->assertEquals(
            "empty1",
            $this->fortuneCompiler->compile($this->view)
        );
        $this->view->setContents('<% forif([0, 1] as $x) %><% forif([] as $y) %>{{$x}}{{$y}}<% forelse %>empty2<% endif %><% forelse %>empty1<% endif %>');
        $this->assertEquals(
            "empty2empty2",
            $this->fortuneCompiler->compile($this->view)
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
            ->method("createView")
            ->willReturn($includedView1);
        $this->viewFactory->expects($this->at(1))
            ->method("createView")
            ->willReturn($includedView2);
        $this->view->setContents('<% include("Foo") %>baz');
        $this->assertEquals(
            "foobarbaz",
            $this->fortuneCompiler->compile($this->view)
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
            ->method("createView")
            ->with("Bar")
            ->willReturn($parent2);
        $this->viewFactory->expects($this->at(1))
            ->method("createView")
            ->with("Foo")
            ->willReturn($parent1);
        $this->view->setContents('<% extends("Bar") %><% part("foo") %><% parent %>blah<% endpart %>');
        $this->assertEquals(
            "barbazblah",
            $this->fortuneCompiler->compile($this->view)
        );
    }

    /**
     * Tests compiling a parent statement
     */
    public function testCompilingParentStatement()
    {
        $parentView = new View("Foo", '<% part("foo") %>bar<% endpart %><% show("foo") %>');
        $this->viewFactory->expects($this->once())
            ->method("createView")
            ->with("Foo")
            ->willReturn($parentView);
        $this->view->setContents('<% extends("Foo") %><% part("foo") %><% parent %>baz<% endpart %>');
        $this->assertEquals(
            "barbaz",
            $this->fortuneCompiler->compile($this->view)
        );
    }

    /**
     * Tests compiling part and show statements
     */
    public function testCompilingPartAndShowStatements()
    {
        $this->view->setContents('<% part("a") %>foo<% endpart %>');
        $this->assertEquals(
            "",
            $this->fortuneCompiler->compile($this->view)
        );
        $this->view->setContents('<% part("a") %>foo<% endpart %><% show("a") %>');
        $this->assertEquals(
            "foo",
            $this->fortuneCompiler->compile($this->view)
        );
        $this->view->setContents('<% part("a") %>foo<% show %>');
        $this->assertEquals(
            "foo",
            $this->fortuneCompiler->compile($this->view)
        );
    }

    /**
     * Tests compiling a view that uses custom tag delimiters
     */
    public function testCompilingViewWithCustomTags()
    {
        $this->view->setContents('^^"A&W"$$ ++"A&W"-- (* if(true) *)foo(* endif *)');
        $this->view->setDelimiters(IView::DELIMITER_TYPE_UNSANITIZED_TAG, ["^^", "$$"]);
        $this->view->setDelimiters(IView::DELIMITER_TYPE_SANITIZED_TAG, ["++", "--"]);
        $this->view->setDelimiters(IView::DELIMITER_TYPE_DIRECTIVE, ["(*", "*)"]);
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "A&W A&amp;W foo",
                $this->fortuneCompiler->compile($this->view)
            )
        );
    }

    /**
     * Tests compiling a while loop
     */
    public function testCompilingWhileLoop()
    {
        $this->view->setContents('<?php $i = 0; ?><% while($i < 2) %>{{$i}}<?php $i++; ?><% endwhile %>');
        $this->assertEquals(
            "01",
            $this->fortuneCompiler->compile($this->view)
        );
    }

    /**
     * Tests that included view's vars are isolated
     */
    public function testIncludedViewVarsAreIsolated()
    {
        $includedView = new View("Foo", 'foo');
        $includedView->setVar("foo", "bar");
        $this->viewFactory->expects($this->at(0))
            ->method("createView")
            ->willReturn($includedView);
        $this->view->setContents('<% include("Foo") %><?php echo isset($foo) ? "set" : "not set"; ?>');
        $this->assertEquals(
            "foonot set",
            $this->fortuneCompiler->compile($this->view)
        );
    }

    /**
     * Tests that included view vars are isolated from outside view vars
     */
    public function testIncludedViewVarsAreIsolatedFromOutsideViewVars()
    {
        $includedView = new View("Foo", '<?php echo isset($foo) ? "set" : "not set"; ?>');
        $this->viewFactory->expects($this->at(0))
            ->method("createView")
            ->willReturn($includedView);
        $this->view->setContents('<% include("Foo") %>');
        $this->view->setVar("foo", "bar");
        $this->assertEquals(
            "not set",
            $this->fortuneCompiler->compile($this->view)
        );
    }

    /**
     * Tests that line breaks are trimmed after compiling
     */
    public function testLineBreaksAreTrimmedAfterCompiling()
    {
        $this->view->setContents(PHP_EOL . PHP_EOL . "\r\nfoo\r\n" . PHP_EOL . PHP_EOL);
        $this->assertEquals('foo', $this->fortuneCompiler->compile($this->view));
    }

    /**
     * Tests overriding a grandparent's part
     */
    public function testOverridingGrandparentPart()
    {
        $grandparentView = new View("Foo", '<% part("foo") %>bar<% endpart %><% show("foo") %>');
        $parentView = new View("Foo", '<% extends("Grandparent") %><% part("foo") %>baz<% endpart %>');
        $this->viewFactory->expects($this->at(0))
            ->method("createView")
            ->with("Parent")
            ->willReturn($parentView);
        $this->viewFactory->expects($this->at(1))
            ->method("createView")
            ->with("Grandparent")
            ->willReturn($grandparentView);
        $this->view->setContents('<% extends("Parent") %><% part("foo") %>blah<% endpart %>');
        $this->assertEquals(
            "blah",
            $this->fortuneCompiler->compile($this->view)
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
            ->method("createView")
            ->with("Parent")
            ->willReturn($parentView);
        $this->viewFactory->expects($this->at(1))
            ->method("createView")
            ->with("Grandparent")
            ->willReturn($grandparentView);
        $this->view->setContents('<% extends("Parent") %>');
        $this->view->setVar("foo", "baz");
        $this->assertEquals(
            "baz",
            $this->fortuneCompiler->compile($this->view)
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
            ->method("createView")
            ->with("Foo")
            ->willReturn($parentView);
        $this->view->setContents('<% extends("Foo") %>');
        $this->view->setVar("foo", "baz");
        $this->assertEquals(
            "baz",
            $this->fortuneCompiler->compile($this->view)
        );
    }

    /**
     * Tests overriding a parent's part
     */
    public function testOverwritingParentPart()
    {
        $includedView = new View("Foo", '<% part("foo") %>bar<% endpart %><% show("foo") %>');
        $this->viewFactory->expects($this->once())
            ->method("createView")
            ->with("Foo")
            ->willReturn($includedView);
        $this->view->setContents('<% extends("Foo") %><% part("foo") %>baz<% endpart %>');
        $this->assertEquals(
            "baz",
            $this->fortuneCompiler->compile($this->view)
        );
    }

    /**
     * Tests that PHP user input is not evaluated
     */
    public function testPhpInputIsNotEvaluated()
    {
        $this->view->setContents('<?php echo $foo; ?>');
        $this->view->setVar("foo", '<?php exit; ?>');
        $this->assertEquals(
            '<?php exit; ?>',
            $this->fortuneCompiler->compile($this->view)
        );
    }

    /**
     * Tests that shared vars override included view's vars
     */
    public function testSharedVarsOverrideIncludedViewVars()
    {
        $includedView = new View("Foo", '<?php echo $foo; ?>');
        $includedView->setVar("foo", "bar");
        $this->viewFactory->expects($this->at(0))
            ->method("createView")
            ->willReturn($includedView);
        $this->view->setContents('<% include("Foo", ["foo" => "baz"]) %>bar');
        $this->assertEquals(
            "bazbar",
            $this->fortuneCompiler->compile($this->view)
        );
    }

    /**
     * Tests that the transpiler is called
     */
    public function testTranspilerIsCalled()
    {
        /** @var ITranspiler|\PHPUnit_Framework_MockObject_MockObject $transpiler */
        $transpiler = $this->createMock(ITranspiler::class);
        $transpiler->expects($this->once())
            ->method("transpile")
            ->with($this->view)
            ->willReturn('<?php echo "bar"; ?>');
        /** @var IViewFactory|\PHPUnit_Framework_MockObject_MockObject $viewFactory */
        $viewFactory = $this->createMock(IViewFactory::class);
        $this->view->setContents('foo');
        $compiler = new FortuneCompiler($transpiler, $viewFactory);
        $this->assertEquals("bar", $compiler->compile($this->view));
        $this->assertSame($viewFactory, $this->view->getVar("__opulenceViewFactory"));
        $this->assertSame($transpiler, $this->view->getVar("__opulenceFortuneTranspiler"));
    }

    /**
     * Tests that view comments are not displayed
     */
    public function testViewCommentsAreNotDisplayed()
    {
        $this->view->setContents('{# Testing #}');
        $this->assertEquals('', $this->fortuneCompiler->compile($this->view));
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

        return $string1 === $string2;
    }
}

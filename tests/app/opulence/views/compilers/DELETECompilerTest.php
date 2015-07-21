<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the view compiler
 */
namespace Opulence\Views\Compilers;
use InvalidArgumentException;
use Opulence\Views\Compilers\SubCompilers\StatementCompiler;
use Opulence\Tests\Views\Mocks\ParentBuilder;
use Opulence\Tests\Views\Compilers\Mocks\Compiler as MockCompiler;
use Opulence\Tests\Views\Compilers\SubCompilers\Mocks\SubCompiler;
use Opulence\Tests\Views\Compilers\Tests\Compiler as BaseCompilerTest;
use Opulence\Views\Caching\Cache;
use Opulence\Views\Factories\ViewFactory;
use Opulence\Views\FortuneView;

class DELETECompilerTest extends BaseCompilerTest
{
    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->markTestSkipped();
    }

    /**
     * Tests changing a parent view to make sure the child gets re-cached
     */
    public function testChangingParentViewToMakeSureChildGetsReCached()
    {
        // Create parent/child views
        file_put_contents(__DIR__ . "/tests/tmp/Master.html", "Foo");
        file_put_contents(__DIR__ . "/tests/tmp/Child.html", '<% extends("Master.html") %>Bar');
        $viewFactory = new ViewFactory($this->fileSystem, __DIR__ . "/tests/tmp");
        // The compiler needs the new view factory because it uses a different path than the built-in one
        $this->compiler = new Compiler(
            new Cache($this->fileSystem, __DIR__ . "/tmp"),
            $viewFactory,
            $this->xssFilter
        );
        $child = $viewFactory->create("Child.html");
        $this->assertEquals("FooBar", $this->compiler->compile($child));
        // Change the master view and make sure the change is picked up
        file_put_contents(__DIR__ . "/tests/tmp/Master.html", "Baz");
        $this->assertEquals("BazBar", $this->compiler->compile($child));
    }

    /**
     * Tests the compiler priorities
     */
    public function testCompilerPriority()
    {
        $view = new FortuneView();
        $view->setContents("");
        // Although this one is registered first, it doesn't have priority
        $this->compiler->registerSubCompiler(new SubCompiler($this->compiler, function ($view, $content)
        {
            return $content . "3";
        }));
        // This one has the second highest priority, so it should be compiled second
        $this->compiler->registerSubCompiler(new SubCompiler($this->compiler, function ($view, $content)
        {
            return $content . "2";
        }), 2);
        // This one has the highest priority, so it should be compiled first
        $this->compiler->registerSubCompiler(new SubCompiler($this->compiler, function ($view, $content)
        {
            return $content . "1";
        }), 1);
        $this->assertEquals("123", $this->compiler->compile($view));
    }

    /**
     * Tests compiling an expression that outputs quotes
     */
    public function testCompilingExpressionThatOutputsQuotes()
    {
        $this->view->setVar("foo", true);
        $this->view->setContents('{{!$foo ? \' class="bar"\' : \'\'!}}');
        $this->assertEquals(' class="bar"', $this->compiler->compile($this->view));
        $this->view->setContents("{{!\$foo ? \" class='bar'\" : \"\"!}}");
        $this->assertEquals(" class='bar'", $this->compiler->compile($this->view));
    }

    /**
     * Tests compiling a part whose value calls a view function
     */
    public function testCompilingPartWhoseValueCallsViewFunction()
    {
        $this->view->setContents('<% show("content") %><% part("content") %>{{round(2.1)}}<%endpart%>');
        $this->compiler->compile($this->view);
        $this->assertEquals("2", $this->compiler->compile($this->view));
    }

    /**
     * Tests compiling a view that uses custom tag delimiters
     */
    public function testCompilingViewWithCustomTags()
    {
        $contents = $this->fileSystem->read(__DIR__ . self::VIEW_PATH_WITH_CUSTOM_TAG_DELIMITERS);
        $this->view->setContents($contents);
        $this->view->setDelimiters(FortuneView::DELIMITER_TYPE_UNSANITIZED_TAG, ["^^", "$$"]);
        $this->view->setDelimiters(FortuneView::DELIMITER_TYPE_SANITIZED_TAG, ["++", "--"]);
        $this->view->setDelimiters(FortuneView::DELIMITER_TYPE_DIRECTIVE, ["(*", "*)"]);
        $this->view->setTag("foo", "Hello");
        $this->view->setTag("bar", "world");
        $this->view->setTag("imSafe", "a&b");
        $functionResult = $this->registerFunction();
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "Hello, world! It worked. ^^blah$$. a&amp;b. me too. c&amp;d. e&f. ++\"g&h\"--. ++ \"i&j\" --. ++blah--. Today escaped is $functionResult and unescaped is $functionResult. .",
                $this->compiler->compile($this->view)
            )
        );
    }

    /**
     * Tests compiling a view that uses the default tag delimiters
     */
    public function testCompilingViewWithDefaultTags()
    {
        $contents = $this->fileSystem->read(__DIR__ . self::VIEW_PATH_WITH_DEFAULT_TAG_DELIMITERS);
        $this->view->setContents($contents);
        $this->view->setTag("foo", "Hello");
        $this->view->setTag("bar", "world");
        $this->view->setTag("imSafe", "a&b");
        $functionResult = $this->registerFunction();
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "Hello, world! It worked. {{!blah!}}. a&amp;b. me too. c&amp;d. e&f. {{\"g&h\"}}. {{ \"i&j\" }}. {{blah}}. Today escaped is $functionResult and unescaped is $functionResult. .",
                $this->compiler->compile($this->view)
            )
        );
    }

    /**
     * Tests compiling a view whose custom tags we didn't set
     */
    public function testCompilingViewWithUnsetCustomTags()
    {
        $contents = $this->fileSystem->read(__DIR__ . self::VIEW_PATH_WITH_CUSTOM_TAG_DELIMITERS);
        $this->view->setContents($contents);
        $this->view->setDelimiters(FortuneView::DELIMITER_TYPE_UNSANITIZED_TAG, ["^^", "$$"]);
        $this->view->setDelimiters(FortuneView::DELIMITER_TYPE_SANITIZED_TAG, ["++", "--"]);
        $this->view->setDelimiters(FortuneView::DELIMITER_TYPE_DIRECTIVE, ["(*", "*)"]);
        $functionResult = $this->registerFunction();
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                ", ! It worked. ^^blah$$. . me too. c&amp;d. e&f. ++\"g&h\"--. ++ \"i&j\" --. ++blah--. Today escaped is $functionResult and unescaped is $functionResult. .",
                $this->compiler->compile($this->view)
            )
        );
    }

    /**
     * Tests compiling a view whose tags we didn't set
     */
    public function testCompilingViewWithUnsetTags()
    {
        $contents = $this->fileSystem->read(__DIR__ . self::VIEW_PATH_WITH_DEFAULT_TAG_DELIMITERS);
        $this->view->setContents($contents);
        $functionResult = $this->registerFunction();
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                ", ! It worked. {{!blah!}}. . me too. c&amp;d. e&f. {{\"g&h\"}}. {{ \"i&j\" }}. {{blah}}. Today escaped is $functionResult and unescaped is $functionResult. .",
                $this->compiler->compile($this->view)
            )
        );
    }

    /**
     * Tests executing a non-existent view function
     */
    public function testExecutingNonExistentViewFunction()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->compiler->callViewFunction("nonExistentFunction");
    }

    /**
     * Tests executing a view function that takes no parameters
     */
    public function testExecutingViewFunctionThatTakesNoParameters()
    {
        $this->compiler->registerViewFunction("foo", function ()
        {
            return "foobar";
        });
        $this->assertEquals("foobar", $this->compiler->callViewFunction("foo"));
    }

    /**
     * Tests executing a view function that takes parameters
     */
    public function testExecutingViewFunctionThatTakesParameters()
    {
        $this->compiler->registerViewFunction("foo", function ($input)
        {
            return "foo" . $input;
        });
        $this->assertEquals("foobar", $this->compiler->callViewFunction("foo", ["bar"]));
    }

    /**
     * Tests extend statement with parent that has a builder
     */
    public function testExtendingParentWithBuilder()
    {
        $this->viewFactory->registerBuilder("Master.html", function ()
        {
            return new ParentBuilder();
        });
        $this->view->setContents(
            $this->fileSystem->read(__DIR__ . self::VIEW_PATH_WITH_EXTEND_AND_PART_STATEMENT)
        );
        $this->compiler->compile($this->view);
        $this->assertEquals(
            '<div>
This is the content
</div>
<div>blah</div>
<div>baz</div>
',
            $this->compiler->compile($this->view)
        );
    }

    /**
     * Tests getting the view functions
     */
    public function testGettingViewFunctions()
    {
        $foo = function ()
        {
            echo "foo";
        };
        $bar = function ()
        {
            echo "bar";
        };
        $compiler = new MockCompiler();
        $compiler->registerViewFunction("foo", $foo);
        $compiler->registerViewFunction("bar", $bar);
        $this->assertEquals([
            "foo" => $foo,
            "bar" => $bar
        ], $compiler->getViewFunctions());
    }

    /**
     * Tests passing in an integer less than 1 for the priority
     */
    public function testIntegerLessThanOnePriority()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->compiler->registerSubCompiler(new SubCompiler($this->compiler, function ($view, $content)
        {
            return $content;
        }), 0);
    }

    /**
     * Tests passing in a non-integer for the priority
     */
    public function testNonIntegerPriority()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->compiler->registerSubCompiler(new SubCompiler($this->compiler, function ($view, $content)
        {
            return $content;
        }), 1.5);
    }

    /**
     * Tests checking that a view is cached after compiling
     */
    public function testViewIsCachedAfterCompiling()
    {
        $statementSubCompiler = new StatementCompiler($this->compiler, $this->viewFactory);
        $contents = $this->fileSystem->read(__DIR__ . self::VIEW_PATH_WITH_EXTEND_STATEMENT);
        $this->view->setContents($contents);
        $compiledStatements = $statementSubCompiler->compile($this->view, $this->view->getContents());
        $this->assertFalse($this->cache->has($compiledStatements, $this->view->getVars(), $this->view->getTags()));
        $this->compiler->compile($this->view);
        $this->assertTrue($this->cache->has($compiledStatements, $this->view->getVars(), $this->view->getTags()));
    }
}
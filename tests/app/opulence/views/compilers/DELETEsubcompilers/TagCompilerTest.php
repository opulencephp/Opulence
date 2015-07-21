<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the tag sub-compiler
 */
namespace Opulence\Views\Compilers\SubCompilers;
use Opulence\HTTP\Requests\Request;
use Opulence\Tests\Mocks\User;
use Opulence\Tests\Views\Compilers\Tests\Compiler as CompilerTest;
use Opulence\Views\Compilers\ViewCompilerException;
use Opulence\Views\FortuneView;

class TagCompilerTest extends CompilerTest
{
    /** @var TagCompiler The sub-compiler to test */
    private $subCompiler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        parent::setUp();

        $this->subCompiler = new TagCompiler($this->compiler, $this->xssFilter);
    }

    /**
     * Tests calling a function on a variable
     */
    public function testCallingFunctionOnVariable()
    {
        // Test object
        $this->view->setVar("request", Request::createFromGlobals());
        $this->view->setContents('{{!$request->isPath("/foo/.*", true) ? \' class="current"\' : ""!}}');
        $this->assertEquals("", $this->subCompiler->compile($this->view, $this->view->getContents()));
        // Test class
        $this->view->setContents(
            '{{!Opulence\Tests\Views\Compilers\SubCompilers\Mocks\ClassWithStaticMethod::foo() == "bar" ? "y" : "n"!}}'
        );
        $this->assertEquals("y", $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests compiling an array variable inside tags
     */
    public function testCompilingArrayVariableInsideTags()
    {
        $delimiters = [
            [
                FortuneView::DEFAULT_OPEN_SANITIZED_TAG_DELIMITER,
                FortuneView::DEFAULT_CLOSE_SANITIZED_TAG_DELIMITER
            ],
            [
                FortuneView::DEFAULT_OPEN_UNSANITIZED_TAG_DELIMITER,
                FortuneView::DEFAULT_CLOSE_UNSANITIZED_TAG_DELIMITER
            ]
        ];
        $viewContents = '<?php foreach(["foo" => ["bar", "a&w"]] as $v): ?>%s$v[1]%s<?php endforeach; ?>';
        $this->view->setContents(sprintf($viewContents, $delimiters[0][0], $delimiters[0][1]));
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "a&amp;w",
                $this->subCompiler->compile($this->view, $this->view->getContents())
            )
        );
        $this->view->setContents(sprintf($viewContents, $delimiters[1][0], $delimiters[1][1]));
        $this->assertEquals("a&w", $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests compiling an escaped tag whose value is an unescaped tag
     */
    public function testCompilingEscapedTagWhoseValueIsUnescapedTag()
    {
        // Order here is important
        // We're testing setting the inner-most tag first, and then the outer tag
        $this->view->setContents("{{!content!}}");
        $this->view->setTag("message", "world");
        $this->view->setTag("content", "Hello, {{message}}!");
        $this->assertEquals(
            "Hello, world!",
            $this->subCompiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling a tag whose value is another tag
     */
    public function testCompilingTagWhoseValueIsAnotherTag()
    {
        // Order here is important
        // We're testing setting the inner-most tag first, and then the outer tag
        $this->view->setContents("{{!content!}}");
        $this->view->setTag("message", "world");
        $this->view->setTag("content", "Hello, {{!message!}}!");
        $this->assertEquals(
            "Hello, world!",
            $this->subCompiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling a variable inside tags
     */
    public function testCompilingVariableInsideTags()
    {
        $delimiters = [
            [
                FortuneView::DEFAULT_OPEN_SANITIZED_TAG_DELIMITER,
                FortuneView::DEFAULT_CLOSE_SANITIZED_TAG_DELIMITER
            ],
            [
                FortuneView::DEFAULT_OPEN_UNSANITIZED_TAG_DELIMITER,
                FortuneView::DEFAULT_CLOSE_UNSANITIZED_TAG_DELIMITER
            ]
        ];
        $viewContents = '<?php foreach(["a&w"] as $v): ?>%s$v%s<?php endforeach; ?>';
        $this->view->setContents(sprintf($viewContents, $delimiters[0][0], $delimiters[0][1]));
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "a&amp;w",
                $this->subCompiler->compile($this->view, $this->view->getContents())
            )
        );
        $this->view->setContents(sprintf($viewContents, $delimiters[1][0], $delimiters[1][1]));
        $this->assertEquals("a&w", $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests compiling a view with PHP code
     */
    public function testCompilingViewWithPHPCode()
    {
        $contents = $this->fileSystem->read(__DIR__ . "/.." . self::VIEW_PATH_WITH_PHP_CODE);
        $this->view->setContents($contents);
        $user1 = new User(1, "foo");
        $user2 = new User(2, "bar");
        $this->view->setTag("listDescription", "usernames");
        $this->view->setVar("users", [$user1, $user2]);
        $this->view->setVar("coolestGuy", "Dave");
        $functionResult = $this->registerFunction();
        $this->assertEquals(
            'TEST List of usernames on ' . $functionResult . ':
<ul>
    <li>foo</li><li>bar</li></ul> 2 items
<br>Dave is a pretty cool guy. Alternative syntax works! I agree. Fake closing PHP tag: ?>',
            $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests compiling an escaped string
     */
    public function testEscapedString()
    {
        $this->view->setContents('{{"a&w"}}');
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "a&amp;w",
                $this->subCompiler->compile($this->view, $this->view->getContents())
            )
        );
        $this->view->setContents("{{'a&w'}}");
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "a&amp;w",
                $this->subCompiler->compile($this->view, $this->view->getContents())
            )
        );
    }

    /**
     * Tests an escaped tag with quotes
     */
    public function testEscapedTagWithQuotes()
    {
        $this->view->setContents('\{{" "}}"');
        $this->assertEquals('{{" "}}"', $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests compiling a view with a function that spans multiple lines
     */
    public function testFunctionThatSpansMultipleLines()
    {
        $this->compiler->registerViewFunction("foo", function ($input)
        {
            return $input . "bar";
        });
        $this->view->setContents("{{
        foo(
        'foo'
        )
        }}");
        $this->assertEquals("foobar", $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests compiling a view with a function that has spaces between the open and close tags
     */
    public function testFunctionWithSpacesBetweenTags()
    {
        $this->view->setContents('{{! foo("bar") !}}');
        $this->compiler->registerViewFunction("foo", function ($input)
        {
            echo $input;
        });
        $this->assertEquals("bar", $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests compiling a non-existent function
     */
    public function testInvalidFunction()
    {
        $this->setExpectedException(ViewCompilerException::class);
        $this->view->setContents('{{ foo() }}');
        $this->subCompiler->compile($this->view, $this->view->getContents());
    }

    /**
     * Tests compiling a view with multiple calls to the same function
     */
    public function testMultipleCallsOfSameFunction()
    {
        $this->compiler->registerViewFunction("foo",
            function ($param1 = null, $param2 = null)
            {
                if($param1 == null && $param2 == null)
                {
                    return "No params";
                }
                elseif($param1 == null)
                {
                    return "Param 2 set";
                }
                elseif($param2 == null)
                {
                    return "Param 1 set";
                }
                else
                {
                    return "Both params set";
                }
            }
        );
        $this->view->setContents(
            '{{!foo()!}}, {{!foo()!}}, {{!foo("bar")!}}, {{!foo(null, "bar")!}}, {{!foo("bar", "blah")!}}'
        );
        $this->assertEquals(
            'No params, No params, Param 1 set, Param 2 set, Both params set',
            $this->subCompiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling nested PHP functions
     */
    public function testNestedPHPFunctions()
    {
        $this->view->setContents('{{!date(strtoupper("y"))!}}');
        $this->assertEquals(date("Y"), $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests compiling nested view functions
     */
    public function testNestedViewFunctions()
    {
        $this->compiler->registerViewFunction("foo", function ()
        {
            return "bar";
        });
        $this->compiler->registerViewFunction("baz", function ($input)
        {
            return strrev($input);
        });
        $this->view->setContents('{{!baz(foo())!}}');
        $this->assertEquals("rab", $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests that only outer quotes are stripped from string literals
     */
    public function testOnlyOuterQuotesGetStrippedFromStringLiterals()
    {
        $this->view->setVar("foo", true);
        $this->view->setContents('{{!$foo ? \' class="bar"\' : \'\'!}}');
        $this->assertEquals(' class="bar"', $this->subCompiler->compile($this->view, $this->view->getContents()));
        $this->view->setContents("{{!\$foo ? \" class='bar'\" : \"\"!}}");
        $this->assertEquals(" class='bar'", $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests compiling a PHP function
     */
    public function testPHPFunction()
    {
        $this->view->setContents('{{ date("Y") }}');
        $this->assertEquals(date("Y"), $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests compiling a PHP function with view function
     */
    public function testPHPFunctionWithViewFunction()
    {
        $this->compiler->registerViewFunction("foo", function ()
        {
            return "Y";
        });
        $this->view->setContents('{{ date(foo()) }}');
        $this->assertEquals(date("Y"), $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests compiling a string literal with escaped quotes
     */
    public function testStringLiteralWithEscapedQuotes()
    {
        // Test escaped strings
        $this->view->setContents("{{'fo\'o'}}");
        $this->assertTrue($this->stringsWithEncodedCharactersEqual(
            "fo&#039;o",
            $this->subCompiler->compile($this->view, $this->view->getContents())
        ));
        $this->view->setContents('{{"fo\"o"}}');
        $this->assertTrue($this->stringsWithEncodedCharactersEqual(
            'fo&quot;o',
            $this->subCompiler->compile($this->view, $this->view->getContents())
        ));
        // Test unescaped strings
        $this->view->setContents("{{!'fo\'o'!}}");
        $this->assertEquals("fo'o", $this->subCompiler->compile($this->view, $this->view->getContents()));
        $this->view->setContents('{{!"fo\"o"!}}');
        $this->assertEquals('fo"o', $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests compiling a view with a tag that spans multiple lines
     */
    public function testTagThatSpansMultipleLines()
    {
        $this->view->setContents("{{
        foo
        }}");
        $this->view->setTag("foo", "bar");
        $this->assertEquals("bar", $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests tag whose value is PHP code
     */
    public function testTagWithPHPValue()
    {
        $this->view->setTag("foo", '$bar->blah();');
        $this->view->setContents('{{!foo!}}');
        $this->assertEquals('$bar->blah();', $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests the ternary operator
     */
    public function testTernaryOperator()
    {
        $this->view->setVar("foo", true);
        $this->view->setContents('{{$foo ? "a&w" : ""}}');
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "a&amp;w",
                $this->subCompiler->compile($this->view, $this->view->getContents())
            )
        );
        $this->view->setContents('{{!$foo ? "a&w" : ""!}}');
        $this->assertEquals("a&w", $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests compiling an unescaped string
     */
    public function testUnescapedString()
    {
        $this->view->setContents('{{!"foo"!}}');
        $this->assertEquals("foo", $this->subCompiler->compile($this->view, $this->view->getContents()));
        $this->view->setContents("{{!'foo'!}}");
        $this->assertEquals("foo", $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests compiling view function
     */
    public function testViewFunction()
    {
        $this->compiler->registerViewFunction("foo", function ()
        {
            return "a&w";
        });
        $this->view->setContents('{{foo()}}');
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "a&amp;w",
                $this->subCompiler->compile($this->view, $this->view->getContents())
            )
        );
        $this->view->setContents('{{!foo()!}}');
        $this->assertEquals("a&w", $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests compiling view function with string input
     */
    public function testViewFunctionWithStringInput()
    {
        $this->compiler->registerViewFunction("foo", function ($input)
        {
            return strrev($input);
        });
        $this->view->setContents('{{!foo("bar")!}}');
        $this->assertEquals("rab", $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests compiling a view that uses custom delimiters
     */
    public function testViewWithCustomDelimiters()
    {
        $contents = $this->fileSystem->read(__DIR__ . "/.." . self::VIEW_PATH_WITH_CUSTOM_TAG_DELIMITERS);
        $this->view->setContents($contents);
        $this->view->setDelimiters(FortuneView::DELIMITER_TYPE_UNSANITIZED_TAG, ["^^", "$$"]);
        $this->view->setDelimiters(FortuneView::DELIMITER_TYPE_SANITIZED_TAG, ["++", "--"]);
        $this->view->setDelimiters(FortuneView::DELIMITER_TYPE_DIRECTIVE, ["(*", "*)"]);
        $this->view->setTag("foo", "Hello");
        $this->view->setTag("bar", "world");
        $this->view->setTag("imSafe", "a&b");
        $this->view->setVar("today", time());
        $this->compiler->registerViewFunction("customDate", function ($date, $format, $args)
        {
            return "foo";
        });
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                'Hello, world! (*show("parttest")*). ^^blah$$. a&amp;b. me too. c&amp;d. e&f. ++"g&h"--. ++ "i&j" --. ++blah--. Today escaped is foo and unescaped is foo. (*part("parttest")*)It worked(*endpart*).',
                $this->subCompiler->compile($this->view, $this->view->getContents())
            )
        );
    }

    /**
     * Tests compiling a view that uses the default delimiters
     */
    public function testViewWithDefaultDelimiters()
    {
        $contents = $this->fileSystem->read(__DIR__ . "/.." . self::VIEW_PATH_WITH_DEFAULT_TAG_DELIMITERS);
        $this->view->setContents($contents);
        $this->view->setTag("foo", "Hello");
        $this->view->setTag("bar", "world");
        $this->view->setTag("imSafe", "a&b");
        $this->view->setVar("today", time());
        $this->compiler->registerViewFunction("customDate", function ($date, $format, $args)
        {
            return "foo";
        });
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                'Hello, world! <%show("parttest")%>. {{!blah!}}. a&amp;b. me too. c&amp;d. e&f. {{"g&h"}}. {{ "i&j" }}. {{blah}}. Today escaped is foo and unescaped is foo. <%part("parttest")%>It worked<%endpart%>.',
                $this->subCompiler->compile($this->view, $this->view->getContents())
            )
        );
    }
}
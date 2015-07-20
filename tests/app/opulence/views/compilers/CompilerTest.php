<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the template compiler
 */
namespace Opulence\Views\Compilers;
use InvalidArgumentException;
use Opulence\Views\Compilers\SubCompilers\StatementCompiler;
use Opulence\Tests\Views\Mocks\ParentBuilder;
use Opulence\Tests\Views\Compilers\Mocks\Compiler as MockCompiler;
use Opulence\Tests\Views\Compilers\SubCompilers\Mocks\SubCompiler;
use Opulence\Tests\Views\Compilers\Tests\Compiler as BaseCompilerTest;
use Opulence\Views\Caching\Cache;
use Opulence\Views\Factories\TemplateFactory;
use Opulence\Views\Template;

class CompilerTest extends BaseCompilerTest
{
    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->markTestSkipped();
    }

    /**
     * Tests changing a parent template to make sure the child gets re-cached
     */
    public function testChangingParentTemplateToMakeSureChildGetsReCached()
    {
        // Create parent/child templates
        file_put_contents(__DIR__ . "/tests/tmp/Master.html", "Foo");
        file_put_contents(__DIR__ . "/tests/tmp/Child.html", '<% extends("Master.html") %>Bar');
        $templateFactory = new TemplateFactory($this->fileSystem, __DIR__ . "/tests/tmp");
        // The compiler needs the new template factory because it uses a different path than the built-in one
        $this->compiler = new Compiler(
            new Cache($this->fileSystem, __DIR__ . "/tmp"),
            $templateFactory,
            $this->xssFilter
        );
        $child = $templateFactory->create("Child.html");
        $this->assertEquals("FooBar", $this->compiler->compile($child));
        // Change the master template and make sure the change is picked up
        file_put_contents(__DIR__ . "/tests/tmp/Master.html", "Baz");
        $this->assertEquals("BazBar", $this->compiler->compile($child));
    }

    /**
     * Tests the compiler priorities
     */
    public function testCompilerPriority()
    {
        $template = new Template();
        $template->setContents("");
        // Although this one is registered first, it doesn't have priority
        $this->compiler->registerSubCompiler(new SubCompiler($this->compiler, function ($template, $content)
        {
            return $content . "3";
        }));
        // This one has the second highest priority, so it should be compiled second
        $this->compiler->registerSubCompiler(new SubCompiler($this->compiler, function ($template, $content)
        {
            return $content . "2";
        }), 2);
        // This one has the highest priority, so it should be compiled first
        $this->compiler->registerSubCompiler(new SubCompiler($this->compiler, function ($template, $content)
        {
            return $content . "1";
        }), 1);
        $this->assertEquals("123", $this->compiler->compile($template));
    }

    /**
     * Tests compiling an expression that outputs quotes
     */
    public function testCompilingExpressionThatOutputsQuotes()
    {
        $this->template->setVar("foo", true);
        $this->template->setContents('{{!$foo ? \' class="bar"\' : \'\'!}}');
        $this->assertEquals(' class="bar"', $this->compiler->compile($this->template));
        $this->template->setContents("{{!\$foo ? \" class='bar'\" : \"\"!}}");
        $this->assertEquals(" class='bar'", $this->compiler->compile($this->template));
    }

    /**
     * Tests compiling a part whose value calls a template function
     */
    public function testCompilingPartWhoseValueCallsTemplateFunction()
    {
        $this->template->setContents('<% show("content") %><% part("content") %>{{round(2.1)}}<%endpart%>');
        $this->compiler->compile($this->template);
        $this->assertEquals("2", $this->compiler->compile($this->template));
    }

    /**
     * Tests compiling a template that uses custom tag delimiters
     */
    public function testCompilingTemplateWithCustomTags()
    {
        $contents = $this->fileSystem->read(__DIR__ . self::TEMPLATE_PATH_WITH_CUSTOM_TAG_DELIMITERS);
        $this->template->setContents($contents);
        $this->template->setDelimiters(Template::DELIMITER_TYPE_UNSANITIZED_TAG, ["^^", "$$"]);
        $this->template->setDelimiters(Template::DELIMITER_TYPE_SANITIZED_TAG, ["++", "--"]);
        $this->template->setDelimiters(Template::DELIMITER_TYPE_DIRECTIVE, ["(*", "*)"]);
        $this->template->setTag("foo", "Hello");
        $this->template->setTag("bar", "world");
        $this->template->setTag("imSafe", "a&b");
        $functionResult = $this->registerFunction();
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "Hello, world! It worked. ^^blah$$. a&amp;b. me too. c&amp;d. e&f. ++\"g&h\"--. ++ \"i&j\" --. ++blah--. Today escaped is $functionResult and unescaped is $functionResult. .",
                $this->compiler->compile($this->template)
            )
        );
    }

    /**
     * Tests compiling a template that uses the default tag delimiters
     */
    public function testCompilingTemplateWithDefaultTags()
    {
        $contents = $this->fileSystem->read(__DIR__ . self::TEMPLATE_PATH_WITH_DEFAULT_TAG_DELIMITERS);
        $this->template->setContents($contents);
        $this->template->setTag("foo", "Hello");
        $this->template->setTag("bar", "world");
        $this->template->setTag("imSafe", "a&b");
        $functionResult = $this->registerFunction();
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "Hello, world! It worked. {{!blah!}}. a&amp;b. me too. c&amp;d. e&f. {{\"g&h\"}}. {{ \"i&j\" }}. {{blah}}. Today escaped is $functionResult and unescaped is $functionResult. .",
                $this->compiler->compile($this->template)
            )
        );
    }

    /**
     * Tests compiling a template whose custom tags we didn't set
     */
    public function testCompilingTemplateWithUnsetCustomTags()
    {
        $contents = $this->fileSystem->read(__DIR__ . self::TEMPLATE_PATH_WITH_CUSTOM_TAG_DELIMITERS);
        $this->template->setContents($contents);
        $this->template->setDelimiters(Template::DELIMITER_TYPE_UNSANITIZED_TAG, ["^^", "$$"]);
        $this->template->setDelimiters(Template::DELIMITER_TYPE_SANITIZED_TAG, ["++", "--"]);
        $this->template->setDelimiters(Template::DELIMITER_TYPE_DIRECTIVE, ["(*", "*)"]);
        $functionResult = $this->registerFunction();
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                ", ! It worked. ^^blah$$. . me too. c&amp;d. e&f. ++\"g&h\"--. ++ \"i&j\" --. ++blah--. Today escaped is $functionResult and unescaped is $functionResult. .",
                $this->compiler->compile($this->template)
            )
        );
    }

    /**
     * Tests compiling a template whose tags we didn't set
     */
    public function testCompilingTemplateWithUnsetTags()
    {
        $contents = $this->fileSystem->read(__DIR__ . self::TEMPLATE_PATH_WITH_DEFAULT_TAG_DELIMITERS);
        $this->template->setContents($contents);
        $functionResult = $this->registerFunction();
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                ", ! It worked. {{!blah!}}. . me too. c&amp;d. e&f. {{\"g&h\"}}. {{ \"i&j\" }}. {{blah}}. Today escaped is $functionResult and unescaped is $functionResult. .",
                $this->compiler->compile($this->template)
            )
        );
    }

    /**
     * Tests executing a non-existent template function
     */
    public function testExecutingNonExistentTemplateFunction()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->compiler->callTemplateFunction("nonExistentFunction");
    }

    /**
     * Tests executing a template function that takes no parameters
     */
    public function testExecutingTemplateFunctionThatTakesNoParameters()
    {
        $this->compiler->registerTemplateFunction("foo", function ()
        {
            return "foobar";
        });
        $this->assertEquals("foobar", $this->compiler->callTemplateFunction("foo"));
    }

    /**
     * Tests executing a template function that takes parameters
     */
    public function testExecutingTemplateFunctionThatTakesParameters()
    {
        $this->compiler->registerTemplateFunction("foo", function ($input)
        {
            return "foo" . $input;
        });
        $this->assertEquals("foobar", $this->compiler->callTemplateFunction("foo", ["bar"]));
    }

    /**
     * Tests extend statement with parent that has a builder
     */
    public function testExtendingParentWithBuilder()
    {
        $this->templateFactory->registerBuilder("Master.html", function ()
        {
            return new ParentBuilder();
        });
        $this->template->setContents(
            $this->fileSystem->read(__DIR__ . self::TEMPLATE_PATH_WITH_EXTEND_AND_PART_STATEMENT)
        );
        $this->compiler->compile($this->template);
        $this->assertEquals(
            '<div>
This is the content
</div>
<div>blah</div>
<div>baz</div>
',
            $this->compiler->compile($this->template)
        );
    }

    /**
     * Tests getting the template functions
     */
    public function testGettingTemplateFunctions()
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
        $compiler->registerTemplateFunction("foo", $foo);
        $compiler->registerTemplateFunction("bar", $bar);
        $this->assertEquals([
            "foo" => $foo,
            "bar" => $bar
        ], $compiler->getTemplateFunctions());
    }

    /**
     * Tests passing in an integer less than 1 for the priority
     */
    public function testIntegerLessThanOnePriority()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->compiler->registerSubCompiler(new SubCompiler($this->compiler, function ($template, $content)
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
        $this->compiler->registerSubCompiler(new SubCompiler($this->compiler, function ($template, $content)
        {
            return $content;
        }), 1.5);
    }

    /**
     * Tests checking that a template is cached after compiling
     */
    public function testTemplateIsCachedAfterCompiling()
    {
        $statementSubCompiler = new StatementCompiler($this->compiler, $this->templateFactory);
        $contents = $this->fileSystem->read(__DIR__ . self::TEMPLATE_PATH_WITH_EXTEND_STATEMENT);
        $this->template->setContents($contents);
        $compiledStatements = $statementSubCompiler->compile($this->template, $this->template->getContents());
        $this->assertFalse($this->cache->has($compiledStatements, $this->template->getVars(), $this->template->getTags()));
        $this->compiler->compile($this->template);
        $this->assertTrue($this->cache->has($compiledStatements, $this->template->getVars(), $this->template->getTags()));
    }
}
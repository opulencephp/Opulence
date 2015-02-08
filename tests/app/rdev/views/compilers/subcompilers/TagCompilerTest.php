<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the tag sub-compiler
 */
namespace RDev\Views\Compilers\SubCompilers;
use RDev\Tests\Views\Compilers\Tests;
use RDev\Views;

class TagCompilerTest extends Tests\Compiler
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
     * Tests compiling an escaped tag whose value is an unescaped tag
     */
    public function testCompilingEscapedTagWhoseValueIsUnescapedTag()
    {
        // Order here is important
        // We're testing setting the inner-most tag first, and then the outer tag
        $this->template->setContents("{{!content!}}");
        $this->template->setTag("message", "world");
        $this->template->setTag("content", "Hello, {{message}}!");
        $this->assertEquals(
            "Hello, world!",
            $this->subCompiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling a string literal
     */
    public function testCompilingStringLiteral()
    {
        $this->template->setContents("{{'foo'}}");
        $this->assertEquals("foo", $this->subCompiler->compile($this->template, $this->template->getContents()));
        $this->template->setContents('{{"foo"}}');
        $this->assertEquals("foo", $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling a string literal with escaped quotes
     */
    public function testCompilingStringLiteralWithEscapedQuotes()
    {
        // Test escaped strings
        $this->template->setContents("{{'fo\'o'}}");
        $this->assertTrue($this->stringsWithEncodedCharactersEqual(
            "fo\&#039;o",
            $this->subCompiler->compile($this->template, $this->template->getContents())
        ));
        $this->template->setContents('{{"fo\"o"}}');
        $this->assertTrue($this->stringsWithEncodedCharactersEqual(
            'fo\&quot;o',
            $this->subCompiler->compile($this->template, $this->template->getContents())
        ));
        // Test unescaped strings
        $this->template->setContents("{{!'fo'o'!}}");
        $this->assertEquals("fo'o", $this->subCompiler->compile($this->template, $this->template->getContents()));
        $this->template->setContents('{{!"fo"o"!}}');
        $this->assertEquals('fo"o', $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling a tag whose value is another tag
     */
    public function testCompilingTagWhoseValueIsAnotherTag()
    {
        // Order here is important
        // We're testing setting the inner-most tag first, and then the outer tag
        $this->template->setContents("{{!content!}}");
        $this->template->setTag("message", "world");
        $this->template->setTag("content", "Hello, {{!message!}}!");
        $this->assertEquals(
            "Hello, world!",
            $this->subCompiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling a template that uses custom delimiters
     */
    public function testCompilingTemplateWithCustomDelimiters()
    {
        $contents = $this->fileSystem->read(__DIR__ . "/.." . self::TEMPLATE_PATH_WITH_CUSTOM_TAG_DELIMITERS);
        $this->template->setContents($contents);
        $this->template->setDelimiters(Views\Template::DELIMITER_TYPE_UNESCAPED_TAG, ["^^", "$$"]);
        $this->template->setDelimiters(Views\Template::DELIMITER_TYPE_ESCAPED_TAG, ["++", "--"]);
        $this->template->setDelimiters(Views\Template::DELIMITER_TYPE_STATEMENT, ["(*", "*)"]);
        $this->template->setTag("foo", "Hello");
        $this->template->setTag("bar", "world");
        $this->template->setTag("imSafe", "a&b");
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                'Hello, world! (*show("parttest")*). ^^blah$$. a&amp;b. me too. c&amp;d. e&f. ++"g&h"--. ++ "i&j" --. ++blah--. Today escaped is  and unescaped is . (*part("parttest")*)It worked(*endpart*).',
                $this->subCompiler->compile($this->template, $this->template->getContents())
            )
        );
    }

    /**
     * Tests compiling a template that uses the default delimiters
     */
    public function testCompilingTemplateWithDefaultDelimiters()
    {
        $contents = $this->fileSystem->read(__DIR__ . "/.." . self::TEMPLATE_PATH_WITH_DEFAULT_TAG_DELIMITERS);
        $this->template->setContents($contents);
        $this->template->setTag("foo", "Hello");
        $this->template->setTag("bar", "world");
        $this->template->setTag("imSafe", "a&b");
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                'Hello, world! {%show("parttest")%}. {{!blah!}}. a&amp;b. me too. c&amp;d. e&f. {{"g&h"}}. {{ "i&j" }}. {{blah}}. Today escaped is  and unescaped is . {%part("parttest")%}It worked{%endpart%}.',
                $this->subCompiler->compile($this->template, $this->template->getContents())
            )
        );
    }

    /**
     * Tests compiling a template with a tag that spans multiple lines
     */
    public function testTagThatSpansMultipleLines()
    {
        $this->template->setContents("{{
        foo
        }}");
        $this->template->setTag("foo", "bar");
        $this->assertEquals("bar", $this->subCompiler->compile($this->template, $this->template->getContents()));
    }
}
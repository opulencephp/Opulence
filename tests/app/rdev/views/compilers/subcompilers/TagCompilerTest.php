<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the tag sub-compiler
 */
namespace RDev\Views\Compilers\SubCompilers;
use RDev\Tests\Views\Compilers\Tests;

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
     * Tests compiling a template that uses custom tags
     */
    public function testCompilingTemplateWithCustomTags()
    {
        $contents = $this->fileSystem->read(__DIR__ . "/.." . self::TEMPLATE_PATH_WITH_CUSTOM_TAGS);
        $this->template->setContents($contents);
        $this->template->setUnescapedOpenTag("^^");
        $this->template->setUnescapedCloseTag("$$");
        $this->template->setEscapedOpenTag("++");
        $this->template->setEscapedCloseTag("--");
        $this->template->setStatementOpenTag("(*");
        $this->template->setStatementCloseTag("*)");
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
     * Tests compiling a template that uses the default tags
     */
    public function testCompilingTemplateWithDefaultTags()
    {
        $contents = $this->fileSystem->read(__DIR__ . "/.." . self::TEMPLATE_PATH_WITH_DEFAULT_TAGS);
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
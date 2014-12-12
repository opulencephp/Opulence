<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the RDev statement sub-compiler
 */
namespace RDev\Views\Compilers\SubCompilers;
use RDev\Files;
use RDev\Tests\Views\Compilers\Tests;
use RDev\Views\Factories;
use RDev\Tests\Views\Mocks;

class StatementTest extends Tests\Compiler
{
    /** @var Statement The sub-compiler to test */
    private $subCompiler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        parent::setUp();

        $this->subCompiler = new Statement($this->compiler, $this->templateFactory);
    }

    /**
     * Tests compiling nested parts
     */
    public function testCompilingNestedParts()
    {
        $this->template->setPart("foo", '{% show("bar") %}blah');
        $this->template->setPart("bar", "baz");
        $this->template->setContents('{% show("foo") %}');
        $this->assertEquals("bazblah", $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests escaping a part statement
     */
    public function testEscapingPartStatement()
    {
        $contents = '\{% part("foo") %}bar{% endpart %}';
        $this->template->setContents($contents);
        $this->assertEquals($contents, $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests extend statement
     */
    public function testExtendStatement()
    {
        $this->template->setContents(
            $this->fileSystem->read(__DIR__ . "/.." . self::TEMPLATE_PATH_WITH_EXTEND_STATEMENT)
        );
        $this->assertEquals(
            "The Header
Hello, world!",
            $this->subCompiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests extend statement with a part statement
     */
    public function testExtendStatementWithPartStatement()
    {
        $this->template->setContents(
            $this->fileSystem->read(__DIR__ . "/.." . self::TEMPLATE_PATH_WITH_EXTEND_AND_PART_STATEMENT)
        );
        $this->assertEquals(
            '<div>
This is the content
</div><div>{{foo}}</div><div><?php if($bar):?>baz<?php endif; ?></div>
',
            $this->subCompiler->compile($this->template, $this->template->getContents())
        );
        $this->assertEquals("
This is the content
",
            $this->template->getPart("content")
        );
    }

    /**
     * Tests extend statement with parent that has a builder
     */
    public function testExtendingParentWithBuilder()
    {
        $this->templateFactory->registerBuilder("Master.html", function()
        {
            return new Mocks\ParentBuilder();
        });
        $this->template->setContents(
            $this->fileSystem->read(__DIR__ . "/.." . self::TEMPLATE_PATH_WITH_EXTEND_AND_PART_STATEMENT)
        );
        $this->assertEquals(
            '<div>
This is the content
</div><div>{{foo}}</div><div><?php if($bar):?>baz<?php endif; ?></div>
',
            $this->subCompiler->compile($this->template, $this->template->getContents())
        );
        $this->assertEquals("
This is the content
",
            $this->template->getPart("content"));
        $this->assertEquals("blah", $this->template->getTag("foo"));
        $this->assertEquals(true, $this->template->getVar("bar"));
    }

    /**
     * Tests multiple show statements with the same part
     */
    public function testMultipleShowStatementsWithSamePart()
    {
        $this->template->setContents('{% show("foo") %} {% show("foo") %}');
        $this->template->setPart("foo", "bar");
        $this->assertEquals("bar bar", $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests extending a template that extends a template
     */
    public function testNestedExtendStatements()
    {
        $this->templateFactory->registerBuilder("Master.html", function()
        {
            return new Mocks\ParentBuilder();
        });
        $this->template->setContents(
            $this->fileSystem->read(__DIR__ . "/.." . self::TEMPLATE_PATH_WITH_NESTED_EXTEND_STATEMENTS)
        );
        $this->assertEquals(
            '<div>
This is the content
</div><div>{{foo}}</div><div><?php if($bar):?>baz<?php endif; ?></div>

Foo',
            $this->subCompiler->compile($this->template, $this->template->getContents())
        );
        $this->assertEquals("
This is the content
",
            $this->template->getPart("content")
        );
        $this->assertEquals("blah", $this->template->getTag("foo"));
        $this->assertEquals(true, $this->template->getVar("bar"));
    }

    /**
     * Tests the part statement with double quotes
     */
    public function testPartStatementWithDoubleQuotes()
    {
        $this->template->setContents('{{foo}} {% part("foo") %}bar{% endpart %}');
        $this->assertEquals("{{foo}} ", $this->subCompiler->compile($this->template, $this->template->getContents()));
        $this->assertEquals("bar", $this->template->getPart("foo"));
    }

    /**
     * Tests the part statement with single quotes
     */
    public function testPartStatementWithSingleQuotes()
    {
        $this->template->setContents("{{foo}} {% part('foo') %}bar{% endpart %}");
        $this->assertEquals("{{foo}} ", $this->subCompiler->compile($this->template, $this->template->getContents()));
        $this->assertEquals("bar", $this->template->getPart("foo"));
    }

    /**
     * Tests that tags are inherited from parents in the correct order
     */
    public function testTagsInheritedInCorrectOrder()
    {
        $this->templateFactory->registerBuilder("Master.html", function()
        {
            return new Mocks\ParentBuilder();
        });
        $this->templateFactory->registerBuilder("TestWithExtendAndPartStatements.html", function()
        {
            return new Mocks\FooBuilder();
        });
        $this->template->setContents(
            $this->fileSystem->read(__DIR__ . "/.." . self::TEMPLATE_PATH_WITH_NESTED_EXTEND_STATEMENTS)
        );
        $this->subCompiler->compile($this->template, $this->template->getContents());
        $this->assertEquals("bar", $this->template->getTag("foo"));
    }
}
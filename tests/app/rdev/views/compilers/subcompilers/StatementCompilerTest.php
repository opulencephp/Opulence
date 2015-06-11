<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the RDev statement sub-compiler
 */
namespace RDev\Views\Compilers\SubCompilers;
use RDev\Tests\Views\Compilers\Tests\Compiler as CompilerTest;
use RDev\Tests\Views\Mocks\FooBuilder;
use RDev\Tests\Views\Mocks\ParentBuilder;

class StatementCompilerTest extends CompilerTest
{
    /** @var StatementCompiler The sub-compiler to test */
    private $subCompiler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        parent::setUp();

        $this->subCompiler = new StatementCompiler($this->compiler, $this->templateFactory);
    }

    /**
     * Tests cleaning up unused statements
     */
    public function testCleaningUpUnusedStatements()
    {
        // Test closed statement
        $this->template->setContents('foo{% foo("hi") %}baz{% endfoo %}bar');
        $this->assertEquals("foobar", $this->subCompiler->compile($this->template, $this->template->getContents()));
        // Test self-closed statement
        $this->template->setContents('foo{% foo("hi") %}bar');
        $this->assertEquals("foobar", $this->subCompiler->compile($this->template, $this->template->getContents()));
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
            return new ParentBuilder();
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
     * Tests a parent statement that reaches back to the grandparent
     */
    public function testGrandparentInheritanceInParentStatement()
    {
        // Try a child that directly inherits from its grandparent
        $this->template->setContents(
            '{% extends("EmptyChild.html") %}{% part("foo") %}{% parent("foo") %}baz{% endpart %}'
        );
        $this->assertEquals(
            "Foobaz",
            $this->subCompiler->compile($this->template, $this->template->getContents())
        );
        // Try a child that inherits from its parent, which inherits from the grandparent
        $this->template->setContents(
            '{% extends("ChildWithDefinedParentStatement.html") %}{% part("foo") %}{% parent("foo") %}baz{% endpart %}'
        );
        $this->assertEquals(
            "Foobarbaz",
            $this->subCompiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests including a template
     */
    public function testIncludeStatement()
    {
        $this->template->setContents(
            $this->fileSystem->read(__DIR__ . "/.." . self::TEMPLATE_PATH_WITH_INCLUDE_STATEMENT)
        );
        $this->assertEquals(
            'FooBar',
            $this->subCompiler->compile($this->template, $this->template->getContents())
        );
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
            return new ParentBuilder();
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
     * Tests including a template that includes a template
     */
    public function testNestedIncludeStatements()
    {
        $this->template->setContents(
            $this->fileSystem->read(__DIR__ . "/.." . self::TEMPLATE_PATH_WITH_NESTED_INCLUDE_STATEMENTS)
        );
        $this->assertEquals(
            'FooBarBaz',
            $this->subCompiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests parent statement
     */
    public function testParentStatement()
    {
        $this->template->setContents(
            $this->fileSystem->read(__DIR__ . "/.." . self::TEMPLATE_PATH_THAT_CALLS_PARENT_PART)
        );
        $this->assertEquals(
            "Foobar",
            $this->subCompiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests that a child's tag is not overwritten by a parent's tag
     */
    public function testParentTagDoesNotOverwriteChildTag()
    {
        $this->templateFactory->registerBuilder("Header.html", function()
        {
            return new ParentBuilder();
        });
        $this->templateFactory->registerBuilder("TestWithExtendStatement.html", function()
        {
            return new FooBuilder();
        });
        $this->template = $this->templateFactory->create("TestWithExtendStatement.html");
        $this->compiler->compile($this->template);
        $this->assertEquals("bar", $this->template->getTag("foo"));
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
            return new ParentBuilder();
        });
        $this->templateFactory->registerBuilder("TestWithExtendAndPartStatements.html", function()
        {
            return new FooBuilder();
        });
        $this->template->setContents(
            $this->fileSystem->read(__DIR__ . "/.." . self::TEMPLATE_PATH_WITH_NESTED_EXTEND_STATEMENTS)
        );
        $this->subCompiler->compile($this->template, $this->template->getContents());
        $this->assertEquals("bar", $this->template->getTag("foo"));
    }

    /**
     * Tests a parent statement that is not defined
     */
    public function testUndefinedParentStatement()
    {
        $this->template->setContents(
            $this->fileSystem->read(__DIR__ . "/.." . self::TEMPLATE_PATH_THAT_CALLS_PARENT_UNDEFINED_PART)
        );
        $this->assertEquals(
            "bar",
            $this->subCompiler->compile($this->template, $this->template->getContents())
        );
    }
}
<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the Opulence statement sub-compiler
 */
namespace Opulence\Views\Compilers\SubCompilers;
use Opulence\Tests\Views\Compilers\Tests\Compiler as CompilerTest;
use Opulence\Tests\Views\Mocks\FooBuilder;
use Opulence\Tests\Views\Mocks\ParentBuilder;

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

        $this->subCompiler = new StatementCompiler($this->compiler, $this->viewFactory);
    }

    /**
     * Tests cleaning up unused statements
     */
    public function testCleaningUpUnusedStatements()
    {
        // Test closed statement
        $this->view->setContents('foo<% foo("hi") %>baz<% endfoo %>bar');
        $this->assertEquals("foobar", $this->subCompiler->compile($this->view, $this->view->getContents()));
        // Test self-closed statement
        $this->view->setContents('foo<% foo("hi") %>bar');
        $this->assertEquals("foobar", $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests compiling nested parts
     */
    public function testCompilingNestedParts()
    {
        $this->view->setPart("foo", '<% show("bar") %>blah');
        $this->view->setPart("bar", "baz");
        $this->view->setContents('<% show("foo") %>');
        $this->assertEquals("bazblah", $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests escaping a part statement
     */
    public function testEscapingPartStatement()
    {
        $contents = '\<% part("foo") %>bar<% endpart %>';
        $this->view->setContents($contents);
        $this->assertEquals($contents, $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests extend statement
     */
    public function testExtendStatement()
    {
        $this->view->setContents(
            $this->fileSystem->read(__DIR__ . "/.." . self::VIEW_PATH_WITH_EXTEND_STATEMENT)
        );
        $this->assertEquals(
            "The Header
Hello, world!",
            $this->subCompiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests extend statement with a part statement
     */
    public function testExtendStatementWithPartStatement()
    {
        $this->view->setContents(
            $this->fileSystem->read(__DIR__ . "/.." . self::VIEW_PATH_WITH_EXTEND_AND_PART_STATEMENT)
        );
        $this->assertEquals(
            '<div>
This is the content
</div>
<div>{{foo}}</div>
<div><?php if($bar):?>baz<?php endif; ?></div>
',
            $this->subCompiler->compile($this->view, $this->view->getContents())
        );
        $this->assertEquals("
This is the content
",
            $this->view->getPart("content")
        );
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
            $this->fileSystem->read(__DIR__ . "/.." . self::VIEW_PATH_WITH_EXTEND_AND_PART_STATEMENT)
        );
        $this->assertEquals(
            '<div>
This is the content
</div>
<div>{{foo}}</div>
<div><?php if($bar):?>baz<?php endif; ?></div>
',
            $this->subCompiler->compile($this->view, $this->view->getContents())
        );
        $this->assertEquals("
This is the content
",
            $this->view->getPart("content"));
        $this->assertEquals("blah", $this->view->getTag("foo"));
        $this->assertEquals(true, $this->view->getVar("bar"));
    }

    /**
     * Tests a parent statement that reaches back to the grandparent
     */
    public function testGrandparentInheritanceInParentStatement()
    {
        // Try a child that directly inherits from its grandparent
        $this->view->setContents(
            '<% extends("EmptyChild.html") %><% part("foo") %><% parent("foo") %>baz<% endpart %>'
        );
        $this->assertEquals(
            "Foobaz",
            $this->subCompiler->compile($this->view, $this->view->getContents())
        );
        // Try a child that inherits from its parent, which inherits from the grandparent
        $this->view->setContents(
            '<% extends("ChildWithDefinedParentStatement.html") %><% part("foo") %><% parent("foo") %>baz<% endpart %>'
        );
        $this->assertEquals(
            "Foobarbaz",
            $this->subCompiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests including a view
     */
    public function testIncludeStatement()
    {
        $this->view->setContents(
            $this->fileSystem->read(__DIR__ . "/.." . self::VIEW_PATH_WITH_INCLUDE_STATEMENT)
        );
        $this->assertEquals(
            'FooBar',
            $this->subCompiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests multiple show statements with the same part
     */
    public function testMultipleShowStatementsWithSamePart()
    {
        $this->view->setContents('<% show("foo") %> <% show("foo") %>');
        $this->view->setPart("foo", "bar");
        $this->assertEquals("bar bar", $this->subCompiler->compile($this->view, $this->view->getContents()));
    }

    /**
     * Tests extending a view that extends a view
     */
    public function testNestedExtendStatements()
    {
        $this->viewFactory->registerBuilder("Master.html", function ()
        {
            return new ParentBuilder();
        });
        $this->view->setContents(
            $this->fileSystem->read(__DIR__ . "/.." . self::VIEW_PATH_WITH_NESTED_EXTEND_STATEMENTS)
        );
        $this->assertEquals(
            '<div>
This is the content
</div>
<div>{{foo}}</div>
<div><?php if($bar):?>baz<?php endif; ?></div>

Foo',
            $this->subCompiler->compile($this->view, $this->view->getContents())
        );
        $this->assertEquals("
This is the content
",
            $this->view->getPart("content")
        );
        $this->assertEquals("blah", $this->view->getTag("foo"));
        $this->assertEquals(true, $this->view->getVar("bar"));
    }

    /**
     * Tests including a view that includes a view
     */
    public function testNestedIncludeStatements()
    {
        $this->view->setContents(
            $this->fileSystem->read(__DIR__ . "/.." . self::VIEW_PATH_WITH_NESTED_INCLUDE_STATEMENTS)
        );
        $this->assertEquals(
            'FooBarBaz',
            $this->subCompiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests parent statement
     */
    public function testParentStatement()
    {
        $this->view->setContents(
            $this->fileSystem->read(__DIR__ . "/.." . self::VIEW_PATH_THAT_CALLS_PARENT_PART)
        );
        $this->assertEquals(
            "Foobar",
            $this->subCompiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests that a child's tag is not overwritten by a parent's tag
     */
    public function testParentTagDoesNotOverwriteChildTag()
    {
        $this->viewFactory->registerBuilder("Header.html", function ()
        {
            return new ParentBuilder();
        });
        $this->viewFactory->registerBuilder("TestWithExtendStatement.html", function ()
        {
            return new FooBuilder();
        });
        $this->view = $this->viewFactory->create("TestWithExtendStatement.html");
        $this->compiler->compile($this->view);
        $this->assertEquals("bar", $this->view->getTag("foo"));
    }

    /**
     * Tests the part statement with double quotes
     */
    public function testPartStatementWithDoubleQuotes()
    {
        $this->view->setContents('{{foo}} <% part("foo") %>bar<% endpart %>');
        $this->assertEquals("{{foo}} ", $this->subCompiler->compile($this->view, $this->view->getContents()));
        $this->assertEquals("bar", $this->view->getPart("foo"));
    }

    /**
     * Tests the part statement with single quotes
     */
    public function testPartStatementWithSingleQuotes()
    {
        $this->view->setContents("{{foo}} <% part('foo') %>bar<% endpart %>");
        $this->assertEquals("{{foo}} ", $this->subCompiler->compile($this->view, $this->view->getContents()));
        $this->assertEquals("bar", $this->view->getPart("foo"));
    }

    /**
     * Tests that tags are inherited from parents in the correct order
     */
    public function testTagsInheritedInCorrectOrder()
    {
        $this->viewFactory->registerBuilder("Master.html", function ()
        {
            return new ParentBuilder();
        });
        $this->viewFactory->registerBuilder("TestWithExtendAndPartStatements.html", function ()
        {
            return new FooBuilder();
        });
        $this->view->setContents(
            $this->fileSystem->read(__DIR__ . "/.." . self::VIEW_PATH_WITH_NESTED_EXTEND_STATEMENTS)
        );
        $this->subCompiler->compile($this->view, $this->view->getContents());
        $this->assertEquals("bar", $this->view->getTag("foo"));
    }

    /**
     * Tests a parent statement that is not defined
     */
    public function testUndefinedParentStatement()
    {
        $this->view->setContents(
            $this->fileSystem->read(__DIR__ . "/.." . self::VIEW_PATH_THAT_CALLS_PARENT_UNDEFINED_PART)
        );
        $this->assertEquals(
            "bar",
            $this->subCompiler->compile($this->view, $this->view->getContents())
        );
    }
}
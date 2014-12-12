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
            '<div>{{!content!}}</div><div>{{foo}}</div><div><?php if($bar):?>baz<?php endif; ?></div>
',
            $this->subCompiler->compile($this->template, $this->template->getContents())
        );
        $this->assertEquals("
This is the content
", $this->template->getTag("content"));
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
            '<div>{{!content!}}</div><div>{{foo}}</div><div><?php if($bar):?>baz<?php endif; ?></div>
',
            $this->subCompiler->compile($this->template, $this->template->getContents())
        );
        $this->assertEquals("
This is the content
", $this->template->getTag("content"));
        $this->assertEquals("bar", $this->template->getTag("foo"));
        $this->assertEquals(true, $this->template->getVar("bar"));
    }

    /**
     * Tests the part statement with double quotes
     */
    public function testPartStatementWithDoubleQuotes()
    {
        $this->template->setContents('{{foo}} {% part("foo") %}bar{% endpart %}');
        $this->assertEquals("{{foo}} ", $this->subCompiler->compile($this->template, $this->template->getContents()));
        $this->assertEquals("bar", $this->template->getTag("foo"));
    }

    /**
     * Tests the part statement with single quotes
     */
    public function testPartStatementWithSingleQuotes()
    {
        $this->template->setContents("{{foo}} {% part('foo') %}bar{% endpart %}");
        $this->assertEquals("{{foo}} ", $this->subCompiler->compile($this->template, $this->template->getContents()));
        $this->assertEquals("bar", $this->template->getTag("foo"));
    }
}
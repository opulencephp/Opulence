<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the mock compiler
 */
namespace RDev\Console\Responses\Compilers;

class MockCompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests compiling a styled message
     */
    public function testCompilingStyledMessage()
    {
        $compiler = new MockCompiler();
        $compiler->setStyled(true);
        $this->assertEquals("<foo>bar</foo>", $compiler->compile("<foo>bar</foo>"));
    }

    /**
     * Tests compiling an unstyled message
     */
    public function testCompilingUnstyledMessage()
    {
        $compiler = new MockCompiler();
        $compiler->setStyled(false);
        $this->assertEquals("<foo>bar</foo>", $compiler->compile("<foo>bar</foo>"));
    }
}
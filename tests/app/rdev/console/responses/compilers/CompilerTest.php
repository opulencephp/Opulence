<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the element compiler
 */
namespace RDev\Console\Responses\Compilers;
use RDev\Console\Responses\Compilers\Lexers;
use RDev\Console\Responses\Compilers\Parsers;
use RDev\Console\Responses\Formatters\Elements;

class CompilerTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Compiler The compiler to use in tests */
    private $compiler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->compiler = new Compiler(new Lexers\Lexer(), new Parsers\Parser());
    }

    /**
     * Tests compiling adjacent elements
     */
    public function testCompilingAdjacentElements()
    {
        $this->compiler->getElements()->add(new Elements\Element("foo", new Elements\Style("green", "white")));
        $this->compiler->getElements()->add(new Elements\Element("bar", new Elements\Style("cyan")));
        $expectedOutput =  "\033[32;47mbaz\033[39;49m\033[36mblah\033[39m";
        $this->assertEquals(
            $expectedOutput,
            $this->compiler->compile("<foo>baz</foo><bar>blah</bar>")
        );
    }

    /**
     * Tests compiling an element with no children
     */
    public function testCompilingElementWithNoChildren()
    {
        $this->compiler->getElements()->add(new Elements\Element("foo", new Elements\Style("green", "white")));
        $this->compiler->getElements()->add(new Elements\Element("bar", new Elements\Style("cyan")));
        $expectedOutput =  "";
        $this->assertEquals(
            $expectedOutput,
            $this->compiler->compile("<foo></foo>")
        );
    }

    /**
     * Tests compiling an element without applying styles
     */
    public function testCompilingElementWithoutApplyingStyles()
    {
        $this->compiler->setStyled(false);
        $this->compiler->getElements()->add(new Elements\Element("foo", new Elements\Style("green", "white")));
        $this->compiler->getElements()->add(new Elements\Element("bar", new Elements\Style("cyan")));
        $this->assertEquals("bazblah", $this->compiler->compile("<foo>baz</foo><bar>blah</bar>"));
    }

    /**
     * Tests compiling an escaped tag at the beginning of the string
     */
    public function testCompilingEscapedTagAtBeginning()
    {
        $this->compiler->getElements()->add(new Elements\Element("foo", new Elements\Style("green")));
        $expectedOutput =  "<bar>";
        $this->assertEquals($expectedOutput, $this->compiler->compile("\\<bar>"));
    }

    /**
     * Tests compiling an escaped tag in between tags
     */
    public function testCompilingEscapedTagInBetweenTags()
    {
        $this->compiler->getElements()->add(new Elements\Element("foo", new Elements\Style("green")));
        $expectedOutput =  "\033[32m<bar>\033[39m";
        $this->assertEquals($expectedOutput, $this->compiler->compile("<foo>\\<bar></foo>"));
    }

    /**
     * Tests compiling nested elements
     */
    public function testCompilingNestedElements()
    {
        $this->compiler->getElements()->add(new Elements\Element("foo", new Elements\Style("green", "white")));
        $this->compiler->getElements()->add(new Elements\Element("bar", new Elements\Style("cyan")));
        $expectedOutput =  "\033[32;47m\033[36mbaz\033[39m\033[39;49m";
        $this->assertEquals(
            $expectedOutput,
            $this->compiler->compile("<foo><bar>baz</bar></foo>")
        );
    }

    /**
     * Tests compiling nested elements with no children
     */
    public function testCompilingNestedElementsWithNoChildren()
    {
        $this->compiler->getElements()->add(new Elements\Element("foo", new Elements\Style("green", "white")));
        $this->compiler->getElements()->add(new Elements\Element("bar", new Elements\Style("cyan")));
        $expectedOutput =  "";
        $this->assertEquals(
            $expectedOutput,
            $this->compiler->compile("<foo><bar></bar></foo>")
        );
    }

    /**
     * Tests compiling nested elements with words in between
     */
    public function testCompilingNestedElementsWithWordsInBetween()
    {
        $this->compiler->getElements()->add(new Elements\Element("foo", new Elements\Style("green", "white")));
        $this->compiler->getElements()->add(new Elements\Element("bar", new Elements\Style("cyan")));
        $expectedOutput =  "\033[32;47mbar\033[39;49m\033[32;47m\033[36mblah\033[39m\033[39;49m\033[32;47mbaz\033[39;49m";
        $this->assertEquals(
            $expectedOutput,
            $this->compiler->compile("<foo>bar<bar>blah</bar>baz</foo>")
        );
    }

    /**
     * Tests compiling plain text
     */
    public function testCompilingPlainText()
    {
        $expectedOutput = "foobar";
        $this->assertEquals(
            $expectedOutput,
            $this->compiler->compile("foobar")
        );
    }

    /**
     * Tests compiling a single element
     */
    public function testCompilingSingleElement()
    {
        $this->compiler->getElements()->add(new Elements\Element("foo", new Elements\Style("green")));
        $expectedOutput =  "\033[32mbar\033[39m";
        $this->assertEquals($expectedOutput, $this->compiler->compile("<foo>bar</foo>"));
    }

    /**
     * Tests compiling unclosed element
     */
    public function testCompilingUnclosedElement()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->compiler->compile("<foo>bar");
    }

    /**
     * Tests compiling unregistered element
     */
    public function testCompilingUnregisteredElement()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->compiler->compile("<foo>bar</foo>");
    }

    /**
     * Tests incorrectly nested elements
     */
    public function testIncorrectlyNestedElements()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->compiler->getElements()->add(new Elements\Element("foo", new Elements\Style("green")));
        $this->compiler->getElements()->add(new Elements\Element("bar", new Elements\Style("blue")));
        $this->compiler->compile("<foo><bar>blah</foo></bar>");
    }
}
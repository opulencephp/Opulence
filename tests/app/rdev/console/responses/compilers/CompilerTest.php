<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the element compiler
 */
namespace RDev\Console\Responses\Compilers;
use RDev\Console\Responses\Formatters\Elements;

class CompilerTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Compiler The compiler to use in tests */
    private $compiler = null;
    /** @var Elements\ElementRegistry The element registry to use in tests */
    private $elementRegistry = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->compiler = new Compiler();
        $this->elementRegistry = new Elements\ElementRegistry();
    }

    /**
     * Tests compiling an escaped tag at the beginning of the string
     */
    public function testCompilingEscapedTagAtBeginning()
    {
        $this->elementRegistry->registerElement(new Elements\Element("foo", new Elements\Style("green")));
        $expectedOutput =  "<bar>";
        $this->assertEquals($expectedOutput, $this->compiler->compile("\\<bar>", $this->elementRegistry));
    }

    /**
     * Tests compiling an escaped tag in between tags
     */
    public function testCompilingEscapedTagInBetweenTags()
    {
        $this->elementRegistry->registerElement(new Elements\Element("foo", new Elements\Style("green")));
        $expectedOutput =  "\033[32m<bar>\033[39m";
        $this->assertEquals($expectedOutput, $this->compiler->compile("<foo>\\<bar></foo>", $this->elementRegistry));
    }

    /**
     * Tests compiling nested elements
     */
    public function testCompilingNestedElements()
    {
        $this->elementRegistry->registerElement(new Elements\Element("foo", new Elements\Style("green", "white")));
        $this->elementRegistry->registerElement(new Elements\Element("bar", new Elements\Style("cyan")));
        $expectedOutput =  "\033[32;47mbar\033[39;49m\033[32;47m\033[36mblah\033[39m\033[39;49m\033[32;47mbaz\033[39;49m";
        $this->assertEquals(
            $expectedOutput,
            $this->compiler->compile("<foo>bar<bar>blah</bar>baz</foo>", $this->elementRegistry)
        );
    }

    /**
     * Tests compiling a single element
     */
    public function testCompilingSingleElement()
    {
        $this->elementRegistry->registerElement(new Elements\Element("foo", new Elements\Style("green")));
        $expectedOutput =  "\033[32mbar\033[39m";
        $this->assertEquals($expectedOutput, $this->compiler->compile("<foo>bar</foo>", $this->elementRegistry));
    }

    /**
     * Tests compiling unclosed element
     */
    public function testCompilingUnclosedElement()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->compiler->compile("<foo>bar", $this->elementRegistry);
    }

    /**
     * Tests compiling unregistered element
     */
    public function testCompilingUnregisteredElement()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->compiler->compile("<foo>bar</foo>", $this->elementRegistry);
    }

    /**
     * Tests incorrectly nested elements
     */
    public function testIncorrectlyNestedElements()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->elementRegistry->registerElement(new Elements\Element("foo", new Elements\Style("green")));
        $this->elementRegistry->registerElement(new Elements\Element("bar", new Elements\Style("blue")));
        $this->compiler->compile("<foo><bar>blah</foo></bar>", $this->elementRegistry);
    }
}
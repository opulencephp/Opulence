<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the response parser
 */
namespace RDev\Console\Responses\Compilers\Parsers;
use RDev\Console\Responses\Compilers\Lexers\Tokens;

class ParserTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Parser The parser to use in tests */
    private $parser = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->parser = new Parser();
    }

    /**
     * Tests incorrectly nested tags
     */
    public function testIncorrectlyNestedTags()
    {
        $this->setExpectedException("\\RuntimeException");
        $tokens = [
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "foo", 1),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "bar", 1),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "blah", 1),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "foo", 1),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "bar", 1),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 1)
        ];
        $this->parser->parse($tokens);
    }

    /**
     * Tests parsing adjacent elements
     */
    public function testParsingAdjacentElements()
    {
        $tokens =  [
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "foo", 1),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "baz", 1),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "foo", 1),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "bar", 1),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "blah", 1),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "bar", 1),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 1)
        ];
        $expectedOutput = new AbstractSyntaxTree();
        $fooNode = new Nodes\TagNode("foo");
        $fooNode->addChild(new Nodes\WordNode("baz"));
        $expectedOutput->getCurrentNode()->addChild($fooNode);
        $barNode = new Nodes\TagNode("bar");
        $barNode->addChild(new Nodes\WordNode("blah"));
        $expectedOutput->getCurrentNode()->addChild($barNode);
        $this->assertEquals(
            $expectedOutput,
            $this->parser->parse($tokens)
        );
    }

    /**
     * Tests parsing an element with no children
     */
    public function testParsingElementWithNoChildren()
    {
        $tokens =  [
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "foo", 1),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "foo", 1),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 1)
        ];
        $expectedOutput = new AbstractSyntaxTree();
        $fooNode = new Nodes\TagNode("foo");
        $expectedOutput->getCurrentNode()->addChild($fooNode);
        $this->assertEquals(
            $expectedOutput,
            $this->parser->parse($tokens)
        );
    }

    /**
     * Tests parsing an escaped tag at the beginning of the string
     */
    public function testParsingEscapedTagAtBeginning()
    {
        $tokens =  [
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "<bar>", 1),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 1)
        ];
        $expectedOutput = new AbstractSyntaxTree();
        $fooNode = new Nodes\WordNode("<bar>");
        $expectedOutput->getCurrentNode()->addChild($fooNode);
        $this->assertEquals($expectedOutput, $this->parser->parse($tokens));
    }

    /**
     * Tests parsing an escaped tag in between tags
     */
    public function testParsingEscapedTagInBetweenTags()
    {
        $tokens =  [
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "foo", 1),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "<bar>", 1),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "foo", 1),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 1)
        ];
        $expectedOutput = new AbstractSyntaxTree();
        $fooNode = new Nodes\TagNode("foo");
        $fooNode->addChild(new Nodes\WordNode("<bar>"));
        $expectedOutput->getCurrentNode()->addChild($fooNode);
        $this->assertEquals($expectedOutput, $this->parser->parse($tokens));
    }

    /**
     * Tests parsing nested elements
     */
    public function testParsingNestedElements()
    {
        $tokens =  [
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "foo", 1),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "bar", 1),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "bar", 1),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "blah", 1),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "bar", 1),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "baz", 1),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "foo", 1),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 1)
        ];
        $expectedOutput = new AbstractSyntaxTree();
        $fooNode = new Nodes\TagNode("foo");
        $fooNode->addChild(new Nodes\WordNode("bar"));
        $barNode = new Nodes\TagNode("bar");
        $barNode->addChild(new Nodes\WordNode("blah"));
        $fooNode->addChild($barNode);
        $fooNode->addChild(new Nodes\WordNode("baz"));
        $expectedOutput->getCurrentNode()->addChild($fooNode);
        $this->assertEquals(
            $expectedOutput,
            $this->parser->parse($tokens)
        );
    }

    /**
     * Tests parsing nested elements surrounded by words
     */
    public function testParsingNestedElementsSurroundedByWords()
    {
        $tokens =  [
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "dave", 1),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "foo", 1),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "bar", 1),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "bar", 1),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "blah", 1),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "bar", 1),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "baz", 1),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "foo", 1),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "young", 1),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 1)
        ];
        $expectedOutput = new AbstractSyntaxTree();
        $expectedOutput->getCurrentNode()->addChild(new Nodes\WordNode("dave"));
        $fooNode = new Nodes\TagNode("foo");
        $fooNode->addChild(new Nodes\WordNode("bar"));
        $barNode = new Nodes\TagNode("bar");
        $barNode->addChild(new Nodes\WordNode("blah"));
        $fooNode->addChild($barNode);
        $fooNode->addChild(new Nodes\WordNode("baz"));
        $expectedOutput->getCurrentNode()->addChild($fooNode);
        $expectedOutput->getCurrentNode()->addChild(new Nodes\WordNode("young"));
        $this->assertEquals(
            $expectedOutput,
            $this->parser->parse($tokens)
        );
    }

    /**
     * Tests parsing nested elements with no children
     */
    public function testParsingNestedElementsWithNoChildren()
    {
        $tokens =  [
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "foo", 1),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "bar", 1),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "bar", 1),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "foo", 1),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 1)
        ];
        $expectedOutput = new AbstractSyntaxTree();
        $fooNode = new Nodes\TagNode("foo");
        $fooNode->addChild(new Nodes\TagNode("bar"));
        $expectedOutput->getCurrentNode()->addChild($fooNode);
        $this->assertEquals(
            $expectedOutput,
            $this->parser->parse($tokens)
        );
    }

    /**
     * Tests parsing plain text
     */
    public function testParsingPlainText()
    {
        $tokens = [
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "foobar", 1),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 1)
        ];
        $expectedOutput = new AbstractSyntaxTree();
        $node = new Nodes\WordNode("foobar");
        $expectedOutput->getCurrentNode()->addChild($node);
        $this->assertEquals(
            $expectedOutput,
            $this->parser->parse($tokens)
        );
    }

    /**
     * Tests parsing a single element
     */
    public function testParsingSingleElement()
    {
        $tokens =  [
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "foo", 1),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "bar", 1),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "foo", 1),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 1)
        ];
        $expectedOutput = new AbstractSyntaxTree();
        $fooNode = new Nodes\TagNode("foo");
        $fooNode->addChild(new Nodes\WordNode("bar"));
        $expectedOutput->getCurrentNode()->addChild($fooNode);
        $this->assertEquals($expectedOutput, $this->parser->parse($tokens));
    }

    /**
     * Tests parsing with an unclosed tag
     */
    public function testParsingWithUnclosedTag()
    {
        $this->setExpectedException("\\RuntimeException");
        $tokens = [
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "foo", 1),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "bar", 1),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 1)
        ];
        $this->parser->parse($tokens);
    }

    /**
     * Tests parsing with an unopened tag
     */
    public function testParsingWithUnopenedTag()
    {
        $this->setExpectedException("\\RuntimeException");
        $tokens = [
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "foo", 0),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "bar", 3),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 9)
        ];
        $this->parser->parse($tokens);
    }
}
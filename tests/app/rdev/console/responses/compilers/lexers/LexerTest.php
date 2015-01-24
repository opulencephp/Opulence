<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the response lexer
 */
namespace RDev\Console\Responses\Compilers\Lexers;

class LexerTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Lexer The lexer to use in tests */
    private $lexer = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->lexer = new Lexer();
    }

    /**
     * Tests lexing adjacent elements
     */
    public function testLexingAdjacentElements()
    {
        $expectedOutput =  [
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "foo", 0),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "baz", 5),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "foo", 8),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "bar", 14),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "blah", 19),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "bar", 23),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 29)
        ];
        $this->assertEquals(
            $expectedOutput,
            $this->lexer->lex("<foo>baz</foo><bar>blah</bar>")
        );
    }

    /**
     * Tests lexing an element with no children
     */
    public function testLexingElementWithNoChildren()
    {
        $expectedOutput =  [
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "foo", 0),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "foo", 5),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 11)
        ];
        $this->assertEquals(
            $expectedOutput,
            $this->lexer->lex("<foo></foo>")
        );
    }

    /**
     * Tests lexing an escaped tag at the beginning of the string
     */
    public function testLexingEscapedTagAtBeginning()
    {
        $expectedOutput =  [
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "<bar>", 1),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 6)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex("\\<bar>"));
    }

    /**
     * Tests lexing an escaped tag in between tags
     */
    public function testLexingEscapedTagInBetweenTags()
    {
        $expectedOutput =  [
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "foo", 0),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "<bar>", 6),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "foo", 11),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 17)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex("<foo>\\<bar></foo>"));
    }

    /**
     * Tests lexing multiple lines
     */
    public function testLexingMultipleLines()
    {
        // We record the EOL length because it differs on OSs
        $eolLength = strlen(PHP_EOL);
        $text = "<foo>" . PHP_EOL . "bar" . PHP_EOL . "</foo>" . PHP_EOL . "baz";
        $expectedOutput = [
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "foo", 0),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, PHP_EOL . "bar" . PHP_EOL, 5),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "foo", 8 + (2 * $eolLength)),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, PHP_EOL . "baz", 16),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 19 + $eolLength)
        ];
        $this->assertEquals(
            $expectedOutput,
            $this->lexer->lex($text)
        );
    }

    /**
     * Tests lexing nested elements
     */
    public function testLexingNestedElements()
    {
        $expectedOutput =  [
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "foo", 0),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "bar", 5),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "bar", 8),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "blah", 13),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "bar", 17),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "baz", 23),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "foo", 26),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 32)
        ];
        $this->assertEquals(
            $expectedOutput,
            $this->lexer->lex("<foo>bar<bar>blah</bar>baz</foo>")
        );
    }

    /**
     * Tests lexing nested elements with no children
     */
    public function testLexingNestedElementsWithNoChildren()
    {
        $expectedOutput =  [
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "foo", 0),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "bar", 5),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "bar", 10),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "foo", 16),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 22)
        ];
        $this->assertEquals(
            $expectedOutput,
            $this->lexer->lex("<foo><bar></bar></foo>")
        );
    }

    /**
     * Tests lexing input with an close tag inside of another close tag
     */
    public function testLexingOpenTagInsideOfCloseTag()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->lexer->lex("<foo></<bar>foo>");
    }

    /**
     * Tests lexing input with an open tag inside of another open tag
     */
    public function testLexingOpenTagInsideOfOpenTag()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->lexer->lex("<foo<bar>>");
    }

    /**
     * Tests lexing plain text
     */
    public function testLexingPlainText()
    {
        $expectedOutput = [
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "foobar", 0),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 6)
        ];
        $this->assertEquals(
            $expectedOutput,
            $this->lexer->lex("foobar")
        );
    }

    /**
     * Tests lexing a single tag
     */
    public function testLexingSingleElement()
    {
        $expectedOutput =  [
            new Tokens\Token(Tokens\TokenTypes::T_TAG_OPEN, "foo", 0),
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "bar", 5),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "foo", 8),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 14)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex("<foo>bar</foo>"));
    }

    /**
     * Tests lexing an unopened tag
     */
    public function testLexingUnopenedTag()
    {
        $expectedOutput =  [
            new Tokens\Token(Tokens\TokenTypes::T_WORD, "foo", 0),
            new Tokens\Token(Tokens\TokenTypes::T_TAG_CLOSE, "bar", 3),
            new Tokens\Token(Tokens\TokenTypes::T_EOF, null, 9)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex("foo</bar>"));
    }
}
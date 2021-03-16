<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Console\Tests\Responses\Compilers\Lexers;

use Opulence\Console\Responses\Compilers\Lexers\Lexer;
use Opulence\Console\Responses\Compilers\Lexers\Tokens\Token;
use Opulence\Console\Responses\Compilers\Lexers\Tokens\TokenTypes;
use RuntimeException;

/**
 * Tests the response lexer
 */
class LexerTest extends \PHPUnit\Framework\TestCase
{
    /** @var Lexer The lexer to use in tests */
    private $lexer = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->lexer = new Lexer();
    }

    /**
     * Tests lexing adjacent elements
     */
    public function testLexingAdjacentElements()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_TAG_OPEN, 'foo', 0),
            new Token(TokenTypes::T_WORD, 'baz', 5),
            new Token(TokenTypes::T_TAG_CLOSE, 'foo', 8),
            new Token(TokenTypes::T_TAG_OPEN, 'bar', 14),
            new Token(TokenTypes::T_WORD, 'blah', 19),
            new Token(TokenTypes::T_TAG_CLOSE, 'bar', 23),
            new Token(TokenTypes::T_EOF, null, 29)
        ];
        $this->assertEquals(
            $expectedOutput,
            $this->lexer->lex('<foo>baz</foo><bar>blah</bar>')
        );
    }

    /**
     * Tests lexing an element with no children
     */
    public function testLexingElementWithNoChildren()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_TAG_OPEN, 'foo', 0),
            new Token(TokenTypes::T_TAG_CLOSE, 'foo', 5),
            new Token(TokenTypes::T_EOF, null, 11)
        ];
        $this->assertEquals(
            $expectedOutput,
            $this->lexer->lex('<foo></foo>')
        );
    }

    /**
     * Tests lexing an escaped tag at the beginning of the string
     */
    public function testLexingEscapedTagAtBeginning()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_WORD, '<bar>', 1),
            new Token(TokenTypes::T_EOF, null, 6)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex('\\<bar>'));
    }

    /**
     * Tests lexing an escaped tag in between tags
     */
    public function testLexingEscapedTagInBetweenTags()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_TAG_OPEN, 'foo', 0),
            new Token(TokenTypes::T_WORD, '<bar>', 6),
            new Token(TokenTypes::T_TAG_CLOSE, 'foo', 11),
            new Token(TokenTypes::T_EOF, null, 17)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex('<foo>\\<bar></foo>'));
    }

    /**
     * Tests lexing multiple lines
     */
    public function testLexingMultipleLines()
    {
        // We record the EOL length because it differs on OSs
        $eolLength = strlen(PHP_EOL);
        $text = '<foo>' . PHP_EOL . 'bar' . PHP_EOL . '</foo>' . PHP_EOL . 'baz';
        $expectedOutput = [
            new Token(TokenTypes::T_TAG_OPEN, 'foo', 0),
            new Token(TokenTypes::T_WORD, PHP_EOL . 'bar' . PHP_EOL, 5),
            new Token(TokenTypes::T_TAG_CLOSE, 'foo', 5 + 3 + (2 * $eolLength)),
            new Token(TokenTypes::T_WORD, PHP_EOL . 'baz', 5 + 3 + (2 * $eolLength) + 6),
            new Token(TokenTypes::T_EOF, null, 5 + 3 + (3 * $eolLength) + 6 + 3)
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
        $expectedOutput = [
            new Token(TokenTypes::T_TAG_OPEN, 'foo', 0),
            new Token(TokenTypes::T_WORD, 'bar', 5),
            new Token(TokenTypes::T_TAG_OPEN, 'bar', 8),
            new Token(TokenTypes::T_WORD, 'blah', 13),
            new Token(TokenTypes::T_TAG_CLOSE, 'bar', 17),
            new Token(TokenTypes::T_WORD, 'baz', 23),
            new Token(TokenTypes::T_TAG_CLOSE, 'foo', 26),
            new Token(TokenTypes::T_EOF, null, 32)
        ];
        $this->assertEquals(
            $expectedOutput,
            $this->lexer->lex('<foo>bar<bar>blah</bar>baz</foo>')
        );
    }

    /**
     * Tests lexing nested elements with no children
     */
    public function testLexingNestedElementsWithNoChildren()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_TAG_OPEN, 'foo', 0),
            new Token(TokenTypes::T_TAG_OPEN, 'bar', 5),
            new Token(TokenTypes::T_TAG_CLOSE, 'bar', 10),
            new Token(TokenTypes::T_TAG_CLOSE, 'foo', 16),
            new Token(TokenTypes::T_EOF, null, 22)
        ];
        $this->assertEquals(
            $expectedOutput,
            $this->lexer->lex('<foo><bar></bar></foo>')
        );
    }

    /**
     * Tests lexing input with an close tag inside of another close tag
     */
    public function testLexingOpenTagInsideOfCloseTag()
    {
        $this->expectException(RuntimeException::class);
        $this->lexer->lex('<foo></<bar>foo>');
    }

    /**
     * Tests lexing input with an open tag inside of another open tag
     */
    public function testLexingOpenTagInsideOfOpenTag()
    {
        $this->expectException(RuntimeException::class);
        $this->lexer->lex('<foo<bar>>');
    }

    /**
     * Tests lexing plain text
     */
    public function testLexingPlainText()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_WORD, 'foobar', 0),
            new Token(TokenTypes::T_EOF, null, 6)
        ];
        $this->assertEquals(
            $expectedOutput,
            $this->lexer->lex('foobar')
        );
    }

    /**
     * Tests lexing a single tag
     */
    public function testLexingSingleElement()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_TAG_OPEN, 'foo', 0),
            new Token(TokenTypes::T_WORD, 'bar', 5),
            new Token(TokenTypes::T_TAG_CLOSE, 'foo', 8),
            new Token(TokenTypes::T_EOF, null, 14)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex('<foo>bar</foo>'));
    }

    /**
     * Tests lexing an unopened tag
     */
    public function testLexingUnopenedTag()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_WORD, 'foo', 0),
            new Token(TokenTypes::T_TAG_CLOSE, 'bar', 3),
            new Token(TokenTypes::T_EOF, null, 9)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex('foo</bar>'));
    }
}

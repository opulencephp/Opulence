<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

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
    private $lexer;

    protected function setUp(): void
    {
        $this->lexer = new Lexer();
    }

    public function testLexingAdjacentElements(): void
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

    public function testLexingElementWithNoChildren(): void
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

    public function testLexingEscapedTagAtBeginning(): void
    {
        $expectedOutput = [
            new Token(TokenTypes::T_WORD, '<bar>', 1),
            new Token(TokenTypes::T_EOF, null, 6)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex('\\<bar>'));
    }

    public function testLexingEscapedTagInBetweenTags(): void
    {
        $expectedOutput = [
            new Token(TokenTypes::T_TAG_OPEN, 'foo', 0),
            new Token(TokenTypes::T_WORD, '<bar>', 6),
            new Token(TokenTypes::T_TAG_CLOSE, 'foo', 11),
            new Token(TokenTypes::T_EOF, null, 17)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex('<foo>\\<bar></foo>'));
    }

    public function testLexingMultipleLines(): void
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

    public function testLexingNestedElements(): void
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

    public function testLexingNestedElementsWithNoChildren(): void
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

    public function testLexingOpenTagInsideOfCloseTag(): void
    {
        $this->expectException(RuntimeException::class);
        $this->lexer->lex('<foo></<bar>foo>');
    }

    public function testLexingOpenTagInsideOfOpenTag(): void
    {
        $this->expectException(RuntimeException::class);
        $this->lexer->lex('<foo<bar>>');
    }

    public function testLexingPlainText(): void
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

    public function testLexingSingleElement(): void
    {
        $expectedOutput = [
            new Token(TokenTypes::T_TAG_OPEN, 'foo', 0),
            new Token(TokenTypes::T_WORD, 'bar', 5),
            new Token(TokenTypes::T_TAG_CLOSE, 'foo', 8),
            new Token(TokenTypes::T_EOF, null, 14)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex('<foo>bar</foo>'));
    }

    public function testLexingUnopenedTag(): void
    {
        $expectedOutput = [
            new Token(TokenTypes::T_WORD, 'foo', 0),
            new Token(TokenTypes::T_TAG_CLOSE, 'bar', 3),
            new Token(TokenTypes::T_EOF, null, 9)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex('foo</bar>'));
    }
}

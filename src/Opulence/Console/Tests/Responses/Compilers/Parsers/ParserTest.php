<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Tests\Responses\Compilers\Parsers;

use Opulence\Console\Responses\Compilers\Lexers\Tokens\Token;
use Opulence\Console\Responses\Compilers\Lexers\Tokens\TokenTypes;
use Opulence\Console\Responses\Compilers\Parsers\AbstractSyntaxTree;
use Opulence\Console\Responses\Compilers\Parsers\Nodes\TagNode;
use Opulence\Console\Responses\Compilers\Parsers\Nodes\WordNode;
use Opulence\Console\Responses\Compilers\Parsers\Parser;
use RuntimeException;

/**
 * Tests the response parser
 */
class ParserTest extends \PHPUnit\Framework\TestCase
{
    /** @var Parser The parser to use in tests */
    private $parser;

    protected function setUp(): void
    {
        $this->parser = new Parser();
    }

    public function testIncorrectlyNestedTags(): void
    {
        $this->expectException(RuntimeException::class);
        $tokens = [
            new Token(TokenTypes::T_TAG_OPEN, 'foo', 1),
            new Token(TokenTypes::T_TAG_OPEN, 'bar', 1),
            new Token(TokenTypes::T_WORD, 'blah', 1),
            new Token(TokenTypes::T_TAG_CLOSE, 'foo', 1),
            new Token(TokenTypes::T_TAG_CLOSE, 'bar', 1),
            new Token(TokenTypes::T_EOF, null, 1)
        ];
        $this->parser->parse($tokens);
    }

    public function testParsingAdjacentElements(): void
    {
        $tokens = [
            new Token(TokenTypes::T_TAG_OPEN, 'foo', 1),
            new Token(TokenTypes::T_WORD, 'baz', 1),
            new Token(TokenTypes::T_TAG_CLOSE, 'foo', 1),
            new Token(TokenTypes::T_TAG_OPEN, 'bar', 1),
            new Token(TokenTypes::T_WORD, 'blah', 1),
            new Token(TokenTypes::T_TAG_CLOSE, 'bar', 1),
            new Token(TokenTypes::T_EOF, null, 1)
        ];
        $expectedOutput = new AbstractSyntaxTree();
        $fooNode = new TagNode('foo');
        $fooNode->addChild(new WordNode('baz'));
        $expectedOutput->getCurrentNode()->addChild($fooNode);
        $barNode = new TagNode('bar');
        $barNode->addChild(new WordNode('blah'));
        $expectedOutput->getCurrentNode()->addChild($barNode);
        $this->assertEquals(
            $expectedOutput,
            $this->parser->parse($tokens)
        );
    }

    public function testParsingElementWithNoChildren(): void
    {
        $tokens = [
            new Token(TokenTypes::T_TAG_OPEN, 'foo', 1),
            new Token(TokenTypes::T_TAG_CLOSE, 'foo', 1),
            new Token(TokenTypes::T_EOF, null, 1)
        ];
        $expectedOutput = new AbstractSyntaxTree();
        $fooNode = new TagNode('foo');
        $expectedOutput->getCurrentNode()->addChild($fooNode);
        $this->assertEquals(
            $expectedOutput,
            $this->parser->parse($tokens)
        );
    }

    public function testParsingEscapedTagAtBeginning(): void
    {
        $tokens = [
            new Token(TokenTypes::T_WORD, '<bar>', 1),
            new Token(TokenTypes::T_EOF, null, 1)
        ];
        $expectedOutput = new AbstractSyntaxTree();
        $fooNode = new WordNode('<bar>');
        $expectedOutput->getCurrentNode()->addChild($fooNode);
        $this->assertEquals($expectedOutput, $this->parser->parse($tokens));
    }

    public function testParsingEscapedTagInBetweenTags(): void
    {
        $tokens = [
            new Token(TokenTypes::T_TAG_OPEN, 'foo', 1),
            new Token(TokenTypes::T_WORD, '<bar>', 1),
            new Token(TokenTypes::T_TAG_CLOSE, 'foo', 1),
            new Token(TokenTypes::T_EOF, null, 1)
        ];
        $expectedOutput = new AbstractSyntaxTree();
        $fooNode = new TagNode('foo');
        $fooNode->addChild(new WordNode('<bar>'));
        $expectedOutput->getCurrentNode()->addChild($fooNode);
        $this->assertEquals($expectedOutput, $this->parser->parse($tokens));
    }

    public function testParsingNestedElements(): void
    {
        $tokens = [
            new Token(TokenTypes::T_TAG_OPEN, 'foo', 1),
            new Token(TokenTypes::T_WORD, 'bar', 1),
            new Token(TokenTypes::T_TAG_OPEN, 'bar', 1),
            new Token(TokenTypes::T_WORD, 'blah', 1),
            new Token(TokenTypes::T_TAG_CLOSE, 'bar', 1),
            new Token(TokenTypes::T_WORD, 'baz', 1),
            new Token(TokenTypes::T_TAG_CLOSE, 'foo', 1),
            new Token(TokenTypes::T_EOF, null, 1)
        ];
        $expectedOutput = new AbstractSyntaxTree();
        $fooNode = new TagNode('foo');
        $fooNode->addChild(new WordNode('bar'));
        $barNode = new TagNode('bar');
        $barNode->addChild(new WordNode('blah'));
        $fooNode->addChild($barNode);
        $fooNode->addChild(new WordNode('baz'));
        $expectedOutput->getCurrentNode()->addChild($fooNode);
        $this->assertEquals(
            $expectedOutput,
            $this->parser->parse($tokens)
        );
    }

    public function testParsingNestedElementsSurroundedByWords(): void
    {
        $tokens = [
            new Token(TokenTypes::T_WORD, 'dave', 1),
            new Token(TokenTypes::T_TAG_OPEN, 'foo', 1),
            new Token(TokenTypes::T_WORD, 'bar', 1),
            new Token(TokenTypes::T_TAG_OPEN, 'bar', 1),
            new Token(TokenTypes::T_WORD, 'blah', 1),
            new Token(TokenTypes::T_TAG_CLOSE, 'bar', 1),
            new Token(TokenTypes::T_WORD, 'baz', 1),
            new Token(TokenTypes::T_TAG_CLOSE, 'foo', 1),
            new Token(TokenTypes::T_WORD, 'young', 1),
            new Token(TokenTypes::T_EOF, null, 1)
        ];
        $expectedOutput = new AbstractSyntaxTree();
        $expectedOutput->getCurrentNode()->addChild(new WordNode('dave'));
        $fooNode = new TagNode('foo');
        $fooNode->addChild(new WordNode('bar'));
        $barNode = new TagNode('bar');
        $barNode->addChild(new WordNode('blah'));
        $fooNode->addChild($barNode);
        $fooNode->addChild(new WordNode('baz'));
        $expectedOutput->getCurrentNode()->addChild($fooNode);
        $expectedOutput->getCurrentNode()->addChild(new WordNode('young'));
        $this->assertEquals(
            $expectedOutput,
            $this->parser->parse($tokens)
        );
    }

    public function testParsingNestedElementsWithNoChildren(): void
    {
        $tokens = [
            new Token(TokenTypes::T_TAG_OPEN, 'foo', 1),
            new Token(TokenTypes::T_TAG_OPEN, 'bar', 1),
            new Token(TokenTypes::T_TAG_CLOSE, 'bar', 1),
            new Token(TokenTypes::T_TAG_CLOSE, 'foo', 1),
            new Token(TokenTypes::T_EOF, null, 1)
        ];
        $expectedOutput = new AbstractSyntaxTree();
        $fooNode = new TagNode('foo');
        $fooNode->addChild(new TagNode('bar'));
        $expectedOutput->getCurrentNode()->addChild($fooNode);
        $this->assertEquals(
            $expectedOutput,
            $this->parser->parse($tokens)
        );
    }

    public function testParsingPlainText(): void
    {
        $tokens = [
            new Token(TokenTypes::T_WORD, 'foobar', 1),
            new Token(TokenTypes::T_EOF, null, 1)
        ];
        $expectedOutput = new AbstractSyntaxTree();
        $node = new WordNode('foobar');
        $expectedOutput->getCurrentNode()->addChild($node);
        $this->assertEquals(
            $expectedOutput,
            $this->parser->parse($tokens)
        );
    }

    public function testParsingSingleElement(): void
    {
        $tokens = [
            new Token(TokenTypes::T_TAG_OPEN, 'foo', 1),
            new Token(TokenTypes::T_WORD, 'bar', 1),
            new Token(TokenTypes::T_TAG_CLOSE, 'foo', 1),
            new Token(TokenTypes::T_EOF, null, 1)
        ];
        $expectedOutput = new AbstractSyntaxTree();
        $fooNode = new TagNode('foo');
        $fooNode->addChild(new WordNode('bar'));
        $expectedOutput->getCurrentNode()->addChild($fooNode);
        $this->assertEquals($expectedOutput, $this->parser->parse($tokens));
    }

    public function testParsingWithUnclosedTag(): void
    {
        $this->expectException(RuntimeException::class);
        $tokens = [
            new Token(TokenTypes::T_TAG_OPEN, 'foo', 1),
            new Token(TokenTypes::T_WORD, 'bar', 1),
            new Token(TokenTypes::T_EOF, null, 1)
        ];
        $this->parser->parse($tokens);
    }

    public function testParsingWithUnopenedTag(): void
    {
        $this->expectException(RuntimeException::class);
        $tokens = [
            new Token(TokenTypes::T_WORD, 'foo', 0),
            new Token(TokenTypes::T_TAG_CLOSE, 'bar', 3),
            new Token(TokenTypes::T_EOF, null, 9)
        ];
        $this->parser->parse($tokens);
    }
}

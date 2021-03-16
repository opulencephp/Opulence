<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

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
    private $parser = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->parser = new Parser();
    }

    /**
     * Tests incorrectly nested tags
     */
    public function testIncorrectlyNestedTags()
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

    /**
     * Tests parsing adjacent elements
     */
    public function testParsingAdjacentElements()
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

    /**
     * Tests parsing an element with no children
     */
    public function testParsingElementWithNoChildren()
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

    /**
     * Tests parsing an escaped tag at the beginning of the string
     */
    public function testParsingEscapedTagAtBeginning()
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

    /**
     * Tests parsing an escaped tag in between tags
     */
    public function testParsingEscapedTagInBetweenTags()
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

    /**
     * Tests parsing nested elements
     */
    public function testParsingNestedElements()
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

    /**
     * Tests parsing nested elements surrounded by words
     */
    public function testParsingNestedElementsSurroundedByWords()
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

    /**
     * Tests parsing nested elements with no children
     */
    public function testParsingNestedElementsWithNoChildren()
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

    /**
     * Tests parsing plain text
     */
    public function testParsingPlainText()
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

    /**
     * Tests parsing a single element
     */
    public function testParsingSingleElement()
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

    /**
     * Tests parsing with an unclosed tag
     */
    public function testParsingWithUnclosedTag()
    {
        $this->expectException(RuntimeException::class);
        $tokens = [
            new Token(TokenTypes::T_TAG_OPEN, 'foo', 1),
            new Token(TokenTypes::T_WORD, 'bar', 1),
            new Token(TokenTypes::T_EOF, null, 1)
        ];
        $this->parser->parse($tokens);
    }

    /**
     * Tests parsing with an unopened tag
     */
    public function testParsingWithUnopenedTag()
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

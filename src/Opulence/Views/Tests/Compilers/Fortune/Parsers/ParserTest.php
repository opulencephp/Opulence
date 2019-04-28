<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\Tests\Compilers\Fortune\Parsers;

use Opulence\Views\Compilers\Fortune\Lexers\Tokens\Token;
use Opulence\Views\Compilers\Fortune\Lexers\Tokens\TokenTypes;
use Opulence\Views\Compilers\Fortune\Parsers\AbstractSyntaxTree;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\CommentNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\DirectiveNameNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\DirectiveNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\ExpressionNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\SanitizedTagNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\UnsanitizedTagNode;
use Opulence\Views\Compilers\Fortune\Parsers\Parser;
use RuntimeException;

/**
 * Tests the view parser
 */
class ParserTest extends \PHPUnit\Framework\TestCase
{
    /** @var Parser The parser to use in tests */
    private $parser;
    /** @var AbstractSyntaxTree The syntax tree to use in tests */
    private $ast;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->parser = new Parser();
        $this->ast = new AbstractSyntaxTree();
    }

    /**
     * Tests that an exception is thrown with an invalid token type
     */
    public function testExceptionThrownWithInvalidTokenType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->parser->parse([new Token('foo', 'bar', 1)]);
    }

    /**
     * Tests that an exception is thrown nested comment
     */
    public function testExceptionThrownWithNestedComment(): void
    {
        $this->expectException(RuntimeException::class);
        $tokens = [
            new Token(TokenTypes::T_COMMENT_OPEN, '{#', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 1),
            new Token(TokenTypes::T_COMMENT_OPEN, '{#', 1),
            new Token(TokenTypes::T_EXPRESSION, 'bar', 1),
            new Token(TokenTypes::T_COMMENT_CLOSE, '#}', 1),
            new Token(TokenTypes::T_COMMENT_CLOSE, '#}', 1)
        ];
        $this->parser->parse($tokens);
    }

    /**
     * Tests that an exception is thrown nested directive
     */
    public function testExceptionThrownWithNestedDirective(): void
    {
        $this->expectException(RuntimeException::class);
        $tokens = [
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'foo', 1),
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'bar', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1)
        ];
        $this->parser->parse($tokens);
    }

    /**
     * Tests that an exception is thrown nested sanitized tag
     */
    public function testExceptionThrownWithNestedSanitizedTag(): void
    {
        $this->expectException(RuntimeException::class);
        $tokens = [
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
            new Token(TokenTypes::T_EXPRESSION, 'bar', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 1)
        ];
        $this->parser->parse($tokens);
    }

    /**
     * Tests that an exception is thrown nested unsanitized tag
     */
    public function testExceptionThrownWithNestedUnsanitizedTag(): void
    {
        $this->expectException(RuntimeException::class);
        $tokens = [
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, 'bar', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1)
        ];
        $this->parser->parse($tokens);
    }

    /**
     * Tests that an exception is thrown with an unclosed comment
     */
    public function testExceptionThrownWithUnclosedComment(): void
    {
        $this->expectException(RuntimeException::class);
        $this->parser->parse([new Token(TokenTypes::T_COMMENT_OPEN, '{#', 1)]);
    }

    /**
     * Tests that an exception is thrown with an unclosed directive
     */
    public function testExceptionThrownWithUnclosedDirective(): void
    {
        $this->expectException(RuntimeException::class);
        $this->parser->parse([new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1)]);
    }

    /**
     * Tests that an exception is thrown with an unclosed sanitized tag
     */
    public function testExceptionThrownWithUnclosedSanitizedTag(): void
    {
        $this->expectException(RuntimeException::class);
        $this->parser->parse([new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1)]);
    }

    /**
     * Tests that an exception is thrown with an unclosed unsanitized tag
     */
    public function testExceptionThrownWithUnclosedUnsanitizedTag(): void
    {
        $this->expectException(RuntimeException::class);
        $this->parser->parse([new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1)]);
    }

    /**
     * Tests that an exception is thrown with an unopened comment
     */
    public function testExceptionThrownWithUnopenedComment(): void
    {
        $this->expectException(RuntimeException::class);
        $this->parser->parse([new Token(TokenTypes::T_COMMENT_CLOSE, '#}', 1)]);
    }

    /**
     * Tests that an exception is thrown with an unopened directive
     */
    public function testExceptionThrownWithUnopenedDirective(): void
    {
        $this->expectException(RuntimeException::class);
        $this->parser->parse([new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1)]);
    }

    /**
     * Tests that an exception is thrown with an unopened sanitized tag
     */
    public function testExceptionThrownWithUnopenedSanitizedTag(): void
    {
        $this->expectException(RuntimeException::class);
        $this->parser->parse([new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 1)]);
    }

    /**
     * Tests that an exception is thrown with an unopened unsanitized tag
     */
    public function testExceptionThrownWithUnopenedUnsanitizedTag(): void
    {
        $this->expectException(RuntimeException::class);
        $this->parser->parse([new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1)]);
    }

    /**
     * Tests parsing a comment
     */
    public function testParsingComment(): void
    {
        $tokens = [
            new Token(TokenTypes::T_COMMENT_OPEN, '{#', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 1),
            new Token(TokenTypes::T_COMMENT_CLOSE, '#}', 1),
        ];
        $commentNode = new CommentNode();
        $commentNode->addChild(new ExpressionNode('foo'));
        $this->ast->getCurrentNode()
            ->addChild($commentNode);
        $this->assertEquals($this->ast, $this->parser->parse($tokens));
    }

    /**
     * Tests parsing a directive with an expression
     */
    public function testParsingDirectiveWithExpression(): void
    {
        $tokens = [
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'foo', 1),
            new Token(TokenTypes::T_EXPRESSION, '("bar")', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1),
        ];
        $directiveNode = new DirectiveNode();
        $directiveNode->addChild(new DirectiveNameNode('foo'));
        $directiveNode->addChild(new ExpressionNode('("bar")'));
        $this->ast->getCurrentNode()
            ->addChild($directiveNode);
        $this->assertEquals($this->ast, $this->parser->parse($tokens));
    }

    /**
     * Tests parsing a directive with no expression
     */
    public function testParsingDirectiveWithNoExpression(): void
    {
        $tokens = [
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'foo', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1),
        ];
        $directiveNode = new DirectiveNode();
        $directiveNode->addChild(new DirectiveNameNode('foo'));
        $this->ast->getCurrentNode()
            ->addChild($directiveNode);
        $this->assertEquals($this->ast, $this->parser->parse($tokens));
    }

    /**
     * Tests parsing empty tokens
     */
    public function testParsingEmptyTokens(): void
    {
        $this->assertEquals($this->ast, $this->parser->parse([]));
    }

    /**
     * Tests parsing an expression
     */
    public function testParsingExpression(): void
    {
        $tokens = [
            new Token(TokenTypes::T_EXPRESSION, 'foo', 1)
        ];
        $this->ast->getCurrentNode()
            ->addChild(new ExpressionNode('foo'));
        $this->assertEquals($this->ast, $this->parser->parse($tokens));
    }

    /**
     * Tests parsing a PHP expression
     */
    public function testParsingPhpExpression(): void
    {
        $tokens = [
            new Token(TokenTypes::T_PHP_TAG_OPEN, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo "foo";', 1),
            new Token(TokenTypes::T_PHP_TAG_CLOSE, '?>', 1)
        ];
        $this->ast->getCurrentNode()
            ->addChild(new ExpressionNode('<?php'))
            ->addChild(new ExpressionNode('echo "foo";'))
            ->addChild(new ExpressionNode('?>'));
        $this->assertEquals($this->ast, $this->parser->parse($tokens));
    }

    /**
     * Tests parsing a sanitized tag
     */
    public function testParsingSanitizedTag(): void
    {
        $tokens = [
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 1),
        ];
        $tagNode = new SanitizedTagNode();
        $tagNode->addChild(new ExpressionNode('foo'));
        $this->ast->getCurrentNode()
            ->addChild($tagNode);
        $this->assertEquals($this->ast, $this->parser->parse($tokens));
    }

    /**
     * Tests parsing statements surrounded by expressions
     */
    public function testParsingStatementsSurroundedByExpressions(): void
    {
        $tokens = [
            new Token(TokenTypes::T_EXPRESSION, 'a', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
            new Token(TokenTypes::T_EXPRESSION, 'b', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 1),
            new Token(TokenTypes::T_EXPRESSION, 'c', 1),
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'd', 1),
            new Token(TokenTypes::T_EXPRESSION, '(e)', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1),
            new Token(TokenTypes::T_EXPRESSION, 'f', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, 'g', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1),
            new Token(TokenTypes::T_EXPRESSION, 'h', 1),
            new Token(TokenTypes::T_COMMENT_OPEN, '{#', 1),
            new Token(TokenTypes::T_EXPRESSION, 'i', 1),
            new Token(TokenTypes::T_COMMENT_CLOSE, '#}', 1),
            new Token(TokenTypes::T_EXPRESSION, 'j', 1),
        ];
        $this->ast->getCurrentNode()->addChild(new ExpressionNode('a'));
        $sanitizedTagNode = new SanitizedTagNode();
        $sanitizedTagNode->addChild(new ExpressionNode('b'));
        $this->ast->getCurrentNode()->addChild($sanitizedTagNode);
        $this->ast->getCurrentNode()->addChild(new ExpressionNode('c'));
        $directiveNode = new DirectiveNode();
        $directiveNode->addChild(new DirectiveNameNode('d'));
        $directiveNode->addChild(new ExpressionNode('(e)'));
        $this->ast->getCurrentNode()->addChild($directiveNode);
        $this->ast->getCurrentNode()->addChild(new ExpressionNode('f'));
        $unsanitizedTagNode = new UnsanitizedTagNode();
        $unsanitizedTagNode->addChild(new ExpressionNode('g'));
        $this->ast->getCurrentNode()->addChild($unsanitizedTagNode);
        $this->ast->getCurrentNode()->addChild(new ExpressionNode('h'));
        $commentNode = new CommentNode();
        $commentNode->addChild(new ExpressionNode('i'));
        $this->ast->getCurrentNode()->addChild($commentNode);
        $this->ast->getCurrentNode()->addChild(new ExpressionNode('j'));
        $this->assertEquals($this->ast, $this->parser->parse($tokens));
    }

    /**
     * Tests parsing an unsanitized tag
     */
    public function testParsingUnsanitizedTag(): void
    {
        $tokens = [
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1),
        ];
        $tagNode = new UnsanitizedTagNode();
        $tagNode->addChild(new ExpressionNode('foo'));
        $this->ast->getCurrentNode()
            ->addChild($tagNode);
        $this->assertEquals($this->ast, $this->parser->parse($tokens));
    }
}

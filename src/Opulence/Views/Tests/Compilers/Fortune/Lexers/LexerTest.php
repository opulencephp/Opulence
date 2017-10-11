<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Views\Tests\Compilers\Fortune\Lexers;

use Opulence\Views\Compilers\Fortune\Lexers\Lexer;
use Opulence\Views\Compilers\Fortune\Lexers\Tokens\Token;
use Opulence\Views\Compilers\Fortune\Lexers\Tokens\TokenTypes;
use Opulence\Views\View;
use RuntimeException;

/**
 * Tests the view lexer
 */
class LexerTest extends \PHPUnit\Framework\TestCase
{
    /** @var Lexer The lexer to use in tests */
    private $lexer = null;
    /** @var View|\PHPUnit_Framework_MockObject_MockObject The view to use in tests */
    private $view = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->lexer = new Lexer();
        $this->view = $this->getMockBuilder(View::class)
            ->setMethods(null)
            ->getMock();
    }

    /**
     * Tests a directive with no expression
     */
    public function testDirectiveWithNoExpression()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'foo', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1)
        ];
        $this->view->setContents('<% foo %>');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests a directive with a non-parenthesis-enclosed expression
     */
    public function testDirectiveWithNonParenthesisEnclosedExpression()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'foo', 1),
            new Token(TokenTypes::T_EXPRESSION, 'bar', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1)
        ];
        $this->view->setContents('<% foo bar %>');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests that an exception is thrown with an unclosed comment tag
     */
    public function testExceptionThrownWithUnclosedCommentTag()
    {
        $this->expectException(RuntimeException::class);
        $this->view->setContents('{# foo');
        $this->lexer->lex($this->view);
    }

    /**
     * Tests that an exception is thrown with an unclosed directive
     */
    public function testExceptionThrownWithUnclosedDirective()
    {
        $this->expectException(RuntimeException::class);
        $this->view->setContents('<% show');
        $this->lexer->lex($this->view);
    }

    /**
     * Tests that an exception is thrown an unclosed parenthesis in a directive
     */
    public function testExceptionThrownWithUnclosedParenthesisInDirective()
    {
        $this->expectException(RuntimeException::class);
        $this->view->setContents('<% show(foo() %>');
        $this->lexer->lex($this->view);
    }

    /**
     * Tests that an exception is thrown with an unclosed sanitized tag
     */
    public function testExceptionThrownWithUnclosedSanitizedTag()
    {
        $this->expectException(RuntimeException::class);
        $this->view->setContents('{{ show');
        $this->lexer->lex($this->view);
    }

    /**
     * Tests that an exception is thrown with an unclosed unsanitized tag
     */
    public function testExceptionThrownWithUnclosedUnsanitizedTag()
    {
        $this->expectException(RuntimeException::class);
        $this->view->setContents('{{! show');
        $this->lexer->lex($this->view);
    }

    /**
     * Tests that an exception is thrown an unopened parenthesis in a directive
     */
    public function testExceptionThrownWithUnopenedParenthesisInDirective()
    {
        $this->expectException(RuntimeException::class);
        $this->view->setContents('<% show(foo)) %>');
        $this->lexer->lex($this->view);
    }

    /**
     * Tests lexing a backslash inside PHP
     */
    public function testLexingBackslashInPhp()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_PHP_TAG_OPEN, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo "\\a";', 1),
            new Token(TokenTypes::T_PHP_TAG_CLOSE, '?>', 1)
        ];
        $this->view->setContents('<?php echo "\\a"; ?>');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests a comment
     */
    public function testLexingComment()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_COMMENT_OPEN, '{#', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 1),
            new Token(TokenTypes::T_COMMENT_CLOSE, '#}', 1)
        ];
        $this->view->setContents('{#foo#}');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
        $this->view->setContents('{# foo #}');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests a directive with an expression
     */
    public function testLexingDirective()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'show', 1),
            new Token(TokenTypes::T_EXPRESSION, '("foo")', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1)
        ];
        $this->view->setContents('<%show("foo")%>');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
        $this->view->setContents('<% show("foo") %>');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
        $this->view->setContents('<% show ("foo") %>');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests a directive inside PHP
     */
    public function testLexingDirectiveInsidePhp()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_PHP_TAG_OPEN, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo "<%";', 1),
            new Token(TokenTypes::T_PHP_TAG_CLOSE, '?>', 1)
        ];
        $this->view->setContents('<?php echo "<%"; ?>');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests a directive surrounded by PHP
     */
    public function testLexingDirectiveSurroundedByPhp()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_PHP_TAG_OPEN, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo "foo";', 1),
            new Token(TokenTypes::T_PHP_TAG_CLOSE, '?>', 1),
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'show', 1),
            new Token(TokenTypes::T_EXPRESSION, '("bar")', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1),
            new Token(TokenTypes::T_PHP_TAG_OPEN, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo "baz";', 1),
            new Token(TokenTypes::T_PHP_TAG_CLOSE, '?>', 1)
        ];
        $this->view->setContents('<?php echo "foo"; ?><% show("bar") %><?php echo "baz"; ?>');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests lexing an escaped statement with a preceding backslash
     */
    public function testLexingEscapedStatementWithPrecedingBackslash()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_EXPRESSION, '\<% foo %>', 1)
        ];
        $this->view->setContents('\\\\<% foo %>');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests lexing escaped statements
     */
    public function testLexingEscapedStatements()
    {
        $texts = ['\<%foo%>', '\{{foo}}', '\{{!foo!}}', '\{#foo#}'];
        $expectedValues = ['<%foo%>', '{{foo}}', '{{!foo!}}', '{#foo#}'];

        foreach ($texts as $index => $text) {
            $expectedOutput = [
                new Token(TokenTypes::T_EXPRESSION, $expectedValues[$index], 1)
            ];
            $this->view->setContents($text);
            $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
        }
    }

    /**
     * Tests lexing statements that span multiple lines
     */
    public function testLexingMultiLineStatements()
    {
        $text = '%s' . PHP_EOL . 'foo' . PHP_EOL . '%s';
        $expectedOutput = [
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'foo', 2),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 3)
        ];
        $this->view->setContents(sprintf($text, '<%', '%>'));
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
        $expectedOutput = [
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 2),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 3)
        ];
        $this->view->setContents(sprintf($text, '{{', '}}'));
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
        $expectedOutput = [
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 2),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 3)
        ];
        $this->view->setContents(sprintf($text, '{{!', '!}}'));
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
        $expectedOutput = [
            new Token(TokenTypes::T_COMMENT_OPEN, '{#', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 2),
            new Token(TokenTypes::T_COMMENT_CLOSE, '#}', 3)
        ];
        $this->view->setContents(sprintf($text, '{#', '#}'));
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests multiple lines of PHP
     */
    public function testLexingMultipleLinesOfPhp()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_PHP_TAG_OPEN, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo "<%";', 2),
            new Token(TokenTypes::T_PHP_TAG_CLOSE, '?>', 3)
        ];
        $this->view->setContents('<?php' . PHP_EOL . ' echo "<%"; ' . PHP_EOL . '?>');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests that native PHP functions are lexed inside statements
     */
    public function testLexingNativePhpFunctionsInsideStatements()
    {
        $text = '%s date("Y") %s';
        $expectedOutput = [
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
            new Token(TokenTypes::T_EXPRESSION, 'date("Y")', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 1)
        ];
        $this->view->setContents(sprintf($text, '{{', '}}'));
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
        $expectedOutput = [
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, 'date("Y")', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1)
        ];
        $this->view->setContents(sprintf($text, '{{!', '!}}'));
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests lexing neighboring escaped statements
     */
    public function testLexingNeighboringEscapedStatements()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_EXPRESSION, '{{foo}}{{!bar!}}<%baz%>{#blah#}', 1)
        ];
        $this->view->setContents('\{{foo}}\{{!bar!}}\<%baz%>\{#blah#}');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests lexing neighboring statements
     */
    public function testLexingNeighboringStatements()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, 'bar', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1),
            new Token(TokenTypes::T_COMMENT_OPEN, '{#', 1),
            new Token(TokenTypes::T_EXPRESSION, 'baz', 1),
            new Token(TokenTypes::T_COMMENT_CLOSE, '#}', 1),
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'blah', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1),
            new Token(TokenTypes::T_EXPRESSION, 'dave', 1)
        ];
        $this->view->setContents('{{foo}}{{!bar!}}{#baz#}<%blah%>dave');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
        $expectedOutput = [
            new Token(TokenTypes::T_EXPRESSION, 'a', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 1),
            new Token(TokenTypes::T_EXPRESSION, 'b', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, 'bar', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1),
            new Token(TokenTypes::T_EXPRESSION, 'c', 1),
            new Token(TokenTypes::T_COMMENT_OPEN, '{#', 1),
            new Token(TokenTypes::T_EXPRESSION, 'baz', 1),
            new Token(TokenTypes::T_COMMENT_CLOSE, '#}', 1),
            new Token(TokenTypes::T_EXPRESSION, 'd', 1),
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'blah', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1),
            new Token(TokenTypes::T_EXPRESSION, 'e', 1)
        ];
        $this->view->setContents('a{{foo}}b{{!bar!}}c{#baz#}d<%blah%>e');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests lexing nested functions
     */
    public function testLexingNestedFunctions()
    {
        $expectedExpression = '$__opulenceFortuneTranspiler->callViewFunction("foo", $__opulenceFortuneTranspiler->callViewFunction("bar", "baz"))';
        $expectedOutput = [
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'show', 1),
            new Token(TokenTypes::T_EXPRESSION, '(' . $expectedExpression . ')', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1)
        ];
        $this->view->setContents('<% show(foo(bar("baz"))) %>');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
        $text = '%s foo(bar("baz")) %s';
        $expectedOutput = [
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
            new Token(TokenTypes::T_EXPRESSION, $expectedExpression, 1),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 1)
        ];
        $this->view->setContents(sprintf($text, '{{', '}}'));
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
        $expectedOutput = [
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, $expectedExpression, 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1)
        ];
        $this->view->setContents(sprintf($text, '{{!', '!}}'));
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
        $expectedOutput = [
            new Token(TokenTypes::T_COMMENT_OPEN, '{#', 1),
            new Token(TokenTypes::T_EXPRESSION, $expectedExpression, 1),
            new Token(TokenTypes::T_COMMENT_CLOSE, '#}', 1)
        ];
        $this->view->setContents(sprintf($text, '{#', '#}'));
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests lexing PHP
     */
    public function testLexingPhp()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_PHP_TAG_OPEN, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo "foo";', 1),
            new Token(TokenTypes::T_PHP_TAG_CLOSE, '?>', 1)
        ];
        // Lex with long open tag and close tag
        $this->view->setContents('<?php echo "foo"; ?>');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
        // Lex with long open tag and without close tag
        $this->view->setContents('<?php echo "foo";');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
        // Lex with short open tag and close tag
        $this->view->setContents('<? echo "foo"; ?>');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
        // Lex with short open tag and without close tag
        $this->view->setContents('<? echo "foo";');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests lexing PHP without close tag
     */
    public function testLexingPhpWithoutCloseTag()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_PHP_TAG_OPEN, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo 1;', 1),
            new Token(TokenTypes::T_PHP_TAG_CLOSE, '?>', 1)
        ];
        $this->view->setContents('<?php echo 1;');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests a sanitized tag
     */
    public function testLexingSanitizedTag()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 1)
        ];
        $this->view->setContents('{{foo}}');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
        $this->view->setContents('{{ foo }}');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests lexing statements whose delimiters are substrings of others
     */
    public function testLexingStatementsWhoseDelimitersAreSubstringsOfOthers()
    {
        $this->view->setDelimiters(View::DELIMITER_TYPE_DIRECTIVE, ['{{{', '}}}']);
        $this->view->setDelimiters(View::DELIMITER_TYPE_SANITIZED_TAG, ['{{', '}}']);
        $this->view->setDelimiters(View::DELIMITER_TYPE_UNSANITIZED_TAG, ['{{!', '!}}']);
        $this->view->setDelimiters(View::DELIMITER_TYPE_COMMENT, ['{{#', '#}}']);
        $expectedOutput = [
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '{{{', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'foo', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '}}}', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
            new Token(TokenTypes::T_EXPRESSION, 'bar', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, 'baz', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1),
            new Token(TokenTypes::T_COMMENT_OPEN, '{{#', 1),
            new Token(TokenTypes::T_EXPRESSION, 'blah', 1),
            new Token(TokenTypes::T_COMMENT_CLOSE, '#}}', 1)
        ];
        $this->view->setContents('{{{foo}}}{{bar}}{{!baz!}}{{#blah#}}');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
        $expectedOutput = [
            new Token(TokenTypes::T_COMMENT_OPEN, '{{#', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 1),
            new Token(TokenTypes::T_COMMENT_CLOSE, '#}}', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, 'bar', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
            new Token(TokenTypes::T_EXPRESSION, 'baz', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 1),
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '{{{', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'blah', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '}}}', 1)
        ];
        $this->view->setContents('{{#foo#}}{{!bar!}}{{baz}}{{{blah}}}');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests lexing a string that looks like a function call
     */
    public function testLexingStringThatLooksLikeFunctionCall()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, '$request->isPath("/docs(/.*)?", true)', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1)
        ];
        $this->view->setContents('{{! $request->isPath("/docs(/.*)?", true) !}}');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests that a text token is created
     */
    public function testLexingText()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_EXPRESSION, 'foo', 1)
        ];
        $this->view->setContents('foo');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests that a text token is created from the buffer
     */
    public function testLexingTextTokenIsCreatedFromBuffer()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_EXPRESSION, 'foo', 1),
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'show', 1),
            new Token(TokenTypes::T_EXPRESSION, '("bar")', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1),
            new Token(TokenTypes::T_EXPRESSION, 'baz', 1)
        ];
        $this->view->setContents('foo<% show("bar") %>baz');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests an unsanitized tag
     */
    public function testLexingUnsanitizedTag()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1)
        ];
        $this->view->setContents('{{!foo!}}');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
        $this->view->setContents('{{! foo !}}');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests that line numbers are respected
     */
    public function testLineNumbersAreRespected()
    {
        $text = 'a' . PHP_EOL .
            '<%' . PHP_EOL . 'b' . PHP_EOL . '(' . PHP_EOL . 'foo' . PHP_EOL . ')' . PHP_EOL . '%>' . PHP_EOL .
            'c' . PHP_EOL .
            '{{' . PHP_EOL . 'd' . PHP_EOL . '}}' . PHP_EOL .
            'e' . PHP_EOL .
            '{{!' . PHP_EOL . 'f' . PHP_EOL . '!}}' . PHP_EOL .
            'g' . PHP_EOL .
            '{#' . PHP_EOL . 'h' . PHP_EOL . '#}' . PHP_EOL .
            'i';
        $expectedOutput = [
            new Token(TokenTypes::T_EXPRESSION, "a\n", 1),
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 2),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'b', 3),
            new Token(TokenTypes::T_EXPRESSION, '(foo)', 4),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 7),
            new Token(TokenTypes::T_EXPRESSION, "\nc\n", 7),
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 9),
            new Token(TokenTypes::T_EXPRESSION, 'd', 10),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 11),
            new Token(TokenTypes::T_EXPRESSION, "\ne\n", 11),
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 13),
            new Token(TokenTypes::T_EXPRESSION, 'f', 14),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 15),
            new Token(TokenTypes::T_EXPRESSION, "\ng\n", 15),
            new Token(TokenTypes::T_COMMENT_OPEN, '{#', 17),
            new Token(TokenTypes::T_EXPRESSION, 'h', 18),
            new Token(TokenTypes::T_COMMENT_CLOSE, '#}', 19),
            new Token(TokenTypes::T_EXPRESSION, "\ni", 19)
        ];
        $this->view->setContents($text);
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    /**
     * Tests that view functions are converted
     */
    public function testViewFunctionsAreConverted()
    {
        $expression = '$foo->bar() $foo::baz() date("Y") foo() foo("bar") foo(bar()) date(foo())';
        $convertedExpression = '$foo->bar() $foo::baz() date("Y") $__opulenceFortuneTranspiler->callViewFunction("foo")' .
            ' $__opulenceFortuneTranspiler->callViewFunction("foo", "bar")' .
            ' $__opulenceFortuneTranspiler->callViewFunction("foo", $__opulenceFortuneTranspiler->callViewFunction("bar"))' .
            ' date($__opulenceFortuneTranspiler->callViewFunction("foo"))';
        // Test sanitized tags
        $this->view->setContents(sprintf('%s ' . $expression . ' %s', '{{', '}}'));
        $this->assertEquals(
            [
                new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
                new Token(TokenTypes::T_EXPRESSION, $convertedExpression, 1),
                new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 1)
            ],
            $this->lexer->lex($this->view)
        );
        // Test unsanitized tags
        $this->view->setContents(sprintf('%s ' . $expression . ' %s', '{{!', '!}}'));
        $this->assertEquals(
            [
                new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
                new Token(TokenTypes::T_EXPRESSION, $convertedExpression, 1),
                new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1)
            ],
            $this->lexer->lex($this->view)
        );
        // Test comments
        $this->view->setContents(sprintf('%s ' . $expression . ' %s', '{#', '#}'));
        $this->assertEquals(
            [
                new Token(TokenTypes::T_COMMENT_OPEN, '{#', 1),
                new Token(TokenTypes::T_EXPRESSION, $convertedExpression, 1),
                new Token(TokenTypes::T_COMMENT_CLOSE, '#}', 1)
            ],
            $this->lexer->lex($this->view)
        );
        // Test PHP tags
        $this->view->setContents(sprintf('%s ' . $expression . ' %s', '<?php', '?>'));
        $this->assertEquals(
            [
                new Token(TokenTypes::T_PHP_TAG_OPEN, '<?php', 1),
                new Token(TokenTypes::T_EXPRESSION, $convertedExpression, 1),
                new Token(TokenTypes::T_PHP_TAG_CLOSE, '?>', 1)
            ],
            $this->lexer->lex($this->view)
        );
        // Test directives
        $this->view->setContents(sprintf('%s show(' . $expression . ') %s', '<%', '%>'));
        $this->assertEquals(
            [
                new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
                new Token(TokenTypes::T_DIRECTIVE_NAME, 'show', 1),
                new Token(TokenTypes::T_EXPRESSION, '(' . $convertedExpression . ')', 1),
                new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1)
            ],
            $this->lexer->lex($this->view)
        );
    }
}

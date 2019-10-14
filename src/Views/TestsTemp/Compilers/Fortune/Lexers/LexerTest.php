<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\TestsTemp\Compilers\Fortune\Lexers;

use Opulence\Views\Compilers\Fortune\Lexers\Lexer;
use Opulence\Views\Compilers\Fortune\Lexers\Tokens\Token;
use Opulence\Views\Compilers\Fortune\Lexers\Tokens\TokenTypes;
use Opulence\Views\View;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;

/**
 * Tests the view lexer
 */
class LexerTest extends \PHPUnit\Framework\TestCase
{
    private Lexer $lexer;
    /** @var View|MockObject The view to use in tests */
    private View $view;

    protected function setUp(): void
    {
        $this->lexer = new Lexer();
        $this->view = $this->getMockBuilder(View::class)
            ->setMethods(null)
            ->getMock();
    }

    public function testDirectiveWithNoExpression(): void
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
    public function testDirectiveWithNonParenthesisEnclosedExpression(): void
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

    public function testExceptionThrownWithUnclosedCommentTag(): void
    {
        $this->expectException(RuntimeException::class);
        $this->view->setContents('{# foo');
        $this->lexer->lex($this->view);
    }

    public function testExceptionThrownWithUnclosedDirective(): void
    {
        $this->expectException(RuntimeException::class);
        $this->view->setContents('<% show');
        $this->lexer->lex($this->view);
    }

    public function testExceptionThrownWithUnclosedParenthesisInDirective(): void
    {
        $this->expectException(RuntimeException::class);
        $this->view->setContents('<% show(foo() %>');
        $this->lexer->lex($this->view);
    }

    public function testExceptionThrownWithUnclosedSanitizedTag(): void
    {
        $this->expectException(RuntimeException::class);
        $this->view->setContents('{{ show');
        $this->lexer->lex($this->view);
    }

    public function testExceptionThrownWithUnclosedUnsanitizedTag(): void
    {
        $this->expectException(RuntimeException::class);
        $this->view->setContents('{{! show');
        $this->lexer->lex($this->view);
    }

    public function testExceptionThrownWithUnopenedParenthesisInDirective(): void
    {
        $this->expectException(RuntimeException::class);
        $this->view->setContents('<% show(foo)) %>');
        $this->lexer->lex($this->view);
    }

    public function testLexingBackslashInPhp(): void
    {
        $expectedOutput = [
            new Token(TokenTypes::T_PHP_TAG_OPEN, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo "\\a";', 1),
            new Token(TokenTypes::T_PHP_TAG_CLOSE, '?>', 1)
        ];
        $this->view->setContents('<?php echo "\\a"; ?>');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    public function testLexingComment(): void
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

    public function testLexingDirective(): void
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

    public function testLexingDirectiveInsidePhp(): void
    {
        $expectedOutput = [
            new Token(TokenTypes::T_PHP_TAG_OPEN, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo "<%";', 1),
            new Token(TokenTypes::T_PHP_TAG_CLOSE, '?>', 1)
        ];
        $this->view->setContents('<?php echo "<%"; ?>');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    public function testLexingDirectiveSurroundedByPhp(): void
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

    public function testLexingEscapedStatementWithPrecedingBackslash(): void
    {
        $expectedOutput = [
            new Token(TokenTypes::T_EXPRESSION, '\<% foo %>', 1)
        ];
        $this->view->setContents('\\\\<% foo %>');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    public function testLexingEscapedStatements(): void
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

    public function testLexingMultiLineStatements(): void
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

    public function testLexingMultipleLinesOfPhp(): void
    {
        $expectedOutput = [
            new Token(TokenTypes::T_PHP_TAG_OPEN, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo "<%";', 2),
            new Token(TokenTypes::T_PHP_TAG_CLOSE, '?>', 3)
        ];
        $this->view->setContents('<?php' . PHP_EOL . ' echo "<%"; ' . PHP_EOL . '?>');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    public function testLexingNativePhpFunctionsInsideStatements(): void
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

    public function testLexingNeighboringEscapedStatements(): void
    {
        $expectedOutput = [
            new Token(TokenTypes::T_EXPRESSION, '{{foo}}{{!bar!}}<%baz%>{#blah#}', 1)
        ];
        $this->view->setContents('\{{foo}}\{{!bar!}}\<%baz%>\{#blah#}');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    public function testLexingNeighboringStatements(): void
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

    public function testLexingNestedFunctions(): void
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

    public function testLexingPhp(): void
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
    }

    public function testLexingPhpWithoutCloseTag(): void
    {
        $expectedOutput = [
            new Token(TokenTypes::T_PHP_TAG_OPEN, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo 1;', 1),
            new Token(TokenTypes::T_PHP_TAG_CLOSE, '?>', 1)
        ];
        $this->view->setContents('<?php echo 1;');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    public function testLexingSanitizedTag(): void
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

    public function testLexingStatementsWhoseDelimitersAreSubstringsOfOthers(): void
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

    public function testLexingStringThatLooksLikeFunctionCall(): void
    {
        $expectedOutput = [
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, '$request->isPath("/docs(/.*)?", true)', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1)
        ];
        $this->view->setContents('{{! $request->isPath("/docs(/.*)?", true) !}}');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    public function testLexingText(): void
    {
        $expectedOutput = [
            new Token(TokenTypes::T_EXPRESSION, 'foo', 1)
        ];
        $this->view->setContents('foo');
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view));
    }

    public function testLexingTextTokenIsCreatedFromBuffer(): void
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

    public function testLexingUnsanitizedTag(): void
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

    public function testLineNumbersAreRespected(): void
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

    public function testViewFunctionsAreConverted(): void
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

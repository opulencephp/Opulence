<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the view lexer
 */
namespace Opulence\Views\Compilers\Fortune\Lexers;
use Opulence\Views\Compilers\Fortune\Lexers\Tokens\Token;
use Opulence\Views\Compilers\Fortune\Lexers\Tokens\TokenTypes;
use Opulence\Views\View;
use RuntimeException;

class LexerTest extends \PHPUnit_Framework_TestCase
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
        $this->view = $this->getMock(View::class, null);
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
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, '<% foo %>'));
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
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, '<% foo bar %>'));
    }

    /**
     * Tests that an exception is thrown with an unclosed directive
     */
    public function testExceptionThrownWithUnclosedDirective()
    {
        $this->setExpectedException(RuntimeException::class);
        $this->lexer->lex($this->view, '<% show');
    }

    /**
     * Tests that an exception is thrown an unclosed parenthesis in a directive
     */
    public function testExceptionThrownWithUnclosedParenthesisInDirective()
    {
        $this->setExpectedException(RuntimeException::class);
        $this->lexer->lex($this->view, '<% show(foo() %>');
    }

    /**
     * Tests that an exception is thrown with an unclosed sanitized tag
     */
    public function testExceptionThrownWithUnclosedSanitizedTag()
    {
        $this->setExpectedException(RuntimeException::class);
        $this->lexer->lex($this->view, '{{ show');
    }

    /**
     * Tests that an exception is thrown with an unclosed unsanitized tag
     */
    public function testExceptionThrownWithUnclosedUnsanitizedTag()
    {
        $this->setExpectedException(RuntimeException::class);
        $this->lexer->lex($this->view, '{{! show');
    }

    /**
     * Tests that an exception is thrown an unopened parenthesis in a directive
     */
    public function testExceptionThrownWithUnopenedParenthesisInDirective()
    {
        $this->setExpectedException(RuntimeException::class);
        $this->lexer->lex($this->view, '<% show(foo)) %>');
    }

    /**
     * Tests lexing a backslash inside PHP
     */
    public function testLexingBackslashInPHP()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_PHP_TAG_OPEN, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo "\\a";', 1),
            new Token(TokenTypes::T_PHP_TAG_CLOSE, '?>', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, '<?php echo "\\a"; ?>'));
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
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, '<%show("foo")%>'));
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, '<% show("foo") %>'));
    }

    /**
     * Tests a directive inside PHP
     */
    public function testLexingDirectiveInsidePHP()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_PHP_TAG_OPEN, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo "<%";', 1),
            new Token(TokenTypes::T_PHP_TAG_CLOSE, '?>', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, '<?php echo "<%"; ?>'));
    }

    /**
     * Tests a directive surrounded by PHP
     */
    public function testLexingDirectiveSurroundedByPHP()
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
        $this->assertEquals(
            $expectedOutput,
            $this->lexer->lex($this->view, '<?php echo "foo"; ?><% show("bar") %><?php echo "baz"; ?>')
        );
    }

    /**
     * Tests lexing an escaped statement with a preceding backslash
     */
    public function testLexingEscapedStatementWithPrecedingBackslash()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_EXPRESSION, '\<% foo %>', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, '\\\\<% foo %>'));
    }

    /**
     * Tests lexing escaped statements
     */
    public function testLexingEscapedStatements()
    {
        $texts = ['\<%foo%>', '\{{foo}}', '\{{!foo!}}'];
        $expectedValues = ['<%foo%>', '{{foo}}', '{{!foo!}}'];

        foreach($texts as $index => $text)
        {
            $expectedOutput = [
                new Token(TokenTypes::T_EXPRESSION, $expectedValues[$index], 1)
            ];
            $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, $text));
        }
    }

    /**
     * Tests lexing statements that span multiple lines
     */
    public function testLexingMultiLineStatements()
    {
        $text = '%s
        foo
        %s';
        $expectedOutput = [
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'foo', 2),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 3)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, sprintf($text, '<%', '%>')));
        $expectedOutput = [
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 2),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 3)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, sprintf($text, '{{', '}}')));
        $expectedOutput = [
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 2),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 3)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, sprintf($text, '{{!', '!}}')));
    }

    /**
     * Tests multiple lines of PHP
     */
    public function testLexingMultipleLinesOfPHP()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_PHP_TAG_OPEN, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo "<%";', 2),
            new Token(TokenTypes::T_PHP_TAG_CLOSE, '?>', 3)
        ];
        $text = '<?php' . PHP_EOL . ' echo "<%"; ' . PHP_EOL . '?>';
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, $text));
    }

    /**
     * Tests that native PHP functions are lexed inside statements
     */
    public function testLexingNativePHPFunctionsInsideStatements()
    {
        $text = '%s date("Y") %s';
        $expectedOutput = [
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
            new Token(TokenTypes::T_EXPRESSION, 'date("Y")', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, sprintf($text, '{{', '}}')));
        $expectedOutput = [
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, 'date("Y")', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, sprintf($text, '{{!', '!}}')));
    }

    /**
     * Tests lexing neighboring escaped statements
     */
    public function testLexingNeighboringEscapedStatements()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_EXPRESSION, '{{foo}}{{!bar!}}<%baz%>', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, '\{{foo}}\{{!bar!}}\<%baz%>'));
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
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'baz', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1),
            new Token(TokenTypes::T_EXPRESSION, 'blah', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, '{{foo}}{{!bar!}}<%baz%>blah'));
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
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'baz', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1),
            new Token(TokenTypes::T_EXPRESSION, 'd', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, 'a{{foo}}b{{!bar!}}c<%baz%>d'));
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
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, '<% show(foo(bar("baz"))) %>'));
        $text = '%s foo(bar("baz")) %s';
        $expectedOutput = [
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
            new Token(TokenTypes::T_EXPRESSION, $expectedExpression, 1),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, sprintf($text, '{{', '}}')));
        $expectedOutput = [
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, $expectedExpression, 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, sprintf($text, '{{!', '!}}')));
    }

    /**
     * Tests lexing PHP
     */
    public function testLexingPHP()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_PHP_TAG_OPEN, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo "foo";', 1),
            new Token(TokenTypes::T_PHP_TAG_CLOSE, '?>', 1)
        ];
        // Lex with long open tag and close tag
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, '<?php echo "foo"; ?>'));
        // Lex with long open tag and without close tag
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, '<?php echo "foo";'));
        // Lex with short open tag and close tag
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, '<? echo "foo"; ?>'));
        // Lex with short open tag and without close tag
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, '<? echo "foo";'));
    }

    /**
     * Tests lexing PHP without close tag
     */
    public function testLexingPHPWithoutCloseTag()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_PHP_TAG_OPEN, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo 1;', 1),
            new Token(TokenTypes::T_PHP_TAG_CLOSE, '?>', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, '<?php echo 1;'));
    }

    /**
     * Tests a sanitized tag with an expression
     */
    public function testLexingSanitizedTag()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, '{{foo}}'));
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, '{{ foo }}'));
    }

    /**
     * Tests lexing statements whose delimiters are substrings of others
     */
    public function testLexingStatementsWhoseDelimitersAreSubstringsOfOthers()
    {
        $this->view->setDelimiters(View::DELIMITER_TYPE_DIRECTIVE, ["{{{", "}}}"]);
        $this->view->setDelimiters(View::DELIMITER_TYPE_SANITIZED_TAG, ["{{", "}}"]);
        $this->view->setDelimiters(View::DELIMITER_TYPE_UNSANITIZED_TAG, ["{{!", "!}}"]);
        $expectedOutput = [
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '{{{', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'foo', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '}}}', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
            new Token(TokenTypes::T_EXPRESSION, 'bar', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, 'baz', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, '{{{foo}}}{{bar}}{{!baz!}}'));
        $expectedOutput = [
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
            new Token(TokenTypes::T_EXPRESSION, 'bar', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 1),
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '{{{', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'baz', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '}}}', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, '{{!foo!}}{{bar}}{{{baz}}}'));
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
        $this->assertEquals(
            $expectedOutput,
            $this->lexer->lex($this->view, '{{! $request->isPath("/docs(/.*)?", true) !}}')
        );
    }

    /**
     * Tests that a text token is created
     */
    public function testLexingText()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_EXPRESSION, 'foo', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, 'foo'));
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
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, 'foo<% show("bar") %>baz'));
    }

    /**
     * Tests an unsanitized tag with an expression
     */
    public function testLexingUnsanitizedTag()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, '{{!foo!}}'));
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, '{{! foo !}}'));
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
            'g';
        $expectedOutput = [
            new Token(TokenTypes::T_EXPRESSION, 'a' . PHP_EOL, 1),
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 2),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'b', 3),
            new Token(TokenTypes::T_EXPRESSION, '(foo)', 4),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 7),
            new Token(TokenTypes::T_EXPRESSION, PHP_EOL . 'c' . PHP_EOL, 7),
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 9),
            new Token(TokenTypes::T_EXPRESSION, 'd', 10),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 11),
            new Token(TokenTypes::T_EXPRESSION, PHP_EOL . 'e' . PHP_EOL, 11),
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 13),
            new Token(TokenTypes::T_EXPRESSION, 'f', 14),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 15),
            new Token(TokenTypes::T_EXPRESSION, PHP_EOL . 'g', 15)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->view, $text));
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
        $this->assertEquals(
            [
                new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
                new Token(TokenTypes::T_EXPRESSION, $convertedExpression, 1),
                new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 1)
            ],
            $this->lexer->lex($this->view, sprintf('%s ' . $expression . ' %s', '{{', '}}'))
        );
        // Test unsanitized tags
        $this->assertEquals(
            [
                new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
                new Token(TokenTypes::T_EXPRESSION, $convertedExpression, 1),
                new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1)
            ],
            $this->lexer->lex($this->view, sprintf('%s ' . $expression . ' %s', '{{!', '!}}'))
        );
        // Test PHP tags
        $this->assertEquals(
            [
                new Token(TokenTypes::T_PHP_TAG_OPEN, '<?php', 1),
                new Token(TokenTypes::T_EXPRESSION, $convertedExpression, 1),
                new Token(TokenTypes::T_PHP_TAG_CLOSE, '?>', 1)
            ],
            $this->lexer->lex($this->view, sprintf('%s ' . $expression . ' %s', '<?php', '?>'))
        );
        // Test directives
        $this->assertEquals(
            [
                new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
                new Token(TokenTypes::T_DIRECTIVE_NAME, 'show', 1),
                new Token(TokenTypes::T_EXPRESSION, '(' . $convertedExpression . ')', 1),
                new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1)
            ],
            $this->lexer->lex($this->view, sprintf('%s show(' . $expression . ') %s', '<%', '%>'))
        );
    }
}
<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the view lexer
 */
namespace Opulence\Views\Compilers\Lexers;
use Opulence\Views\Compilers\Lexers\Tokens\Token;
use Opulence\Views\Compilers\Lexers\Tokens\TokenTypes;
use Opulence\Views\Template;
use RuntimeException;

class LexerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Lexer The lexer to use in tests */
    private $lexer = null;
    /** @var Template|\PHPUnit_Framework_MockObject_MockObject The template to use in tests */
    private $template = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->lexer = new Lexer();
        $this->template = $this->getMock(Template::class, null);
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
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, '<% foo %>'));
    }

    /**
     * Tests that an exception is thrown with an unclosed directive
     */
    public function testExceptionThrownWithUnclosedDirective()
    {
        $this->setExpectedException(RuntimeException::class);
        $this->lexer->lex($this->template, '<% show');
    }

    /**
     * Tests that an exception is thrown an unclosed parenthesis in a directive
     */
    public function testExceptionThrownWithUnclosedParenthesisInDirective()
    {
        $this->setExpectedException(RuntimeException::class);
        $this->lexer->lex($this->template, '<% show(foo() %>');
    }

    /**
     * Tests that an exception is thrown with an unclosed sanitized tag
     */
    public function testExceptionThrownWithUnclosedSanitizedTag()
    {
        $this->setExpectedException(RuntimeException::class);
        $this->lexer->lex($this->template, '{{ show');
    }

    /**
     * Tests that an exception is thrown with an unclosed unsanitized tag
     */
    public function testExceptionThrownWithUnclosedUnsanitizedTag()
    {
        $this->setExpectedException(RuntimeException::class);
        $this->lexer->lex($this->template, '{{! show');
    }

    /**
     * Tests that an exception is thrown an unopened parenthesis in a directive
     */
    public function testExceptionThrownWithUnopenedParenthesisInDirective()
    {
        $this->setExpectedException(RuntimeException::class);
        $this->lexer->lex($this->template, '<% show(foo)) %>');
    }

    /**
     * Tests lexing a backslash inside PHP
     */
    public function testLexingBackslashInPHP()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_PHP_OPEN_TAG, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo "\\";', 1),
            new Token(TokenTypes::T_PHP_CLOSE_TAG, '?>', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, '<?php echo "\\"; ?>'));
    }

    /**
     * Tests a directive with an expression
     */
    public function testLexingDirective()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'show', 1),
            new Token(TokenTypes::T_EXPRESSION, '"foo"', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, '<%show("foo")%>'));
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, '<% show("foo") %>'));
    }

    /**
     * Tests a directive inside PHP
     */
    public function testLexingDirectiveInsidePHP()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_PHP_OPEN_TAG, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo "<%";', 1),
            new Token(TokenTypes::T_PHP_CLOSE_TAG, '?>', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, '<?php echo "<%"; ?>'));
    }

    /**
     * Tests a directive surrounded by PHP
     */
    public function testLexingDirectiveSurroundedByPHP()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_PHP_OPEN_TAG, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo "foo";', 1),
            new Token(TokenTypes::T_PHP_CLOSE_TAG, '?>', 1),
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'show', 1),
            new Token(TokenTypes::T_EXPRESSION, '"bar"', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1),
            new Token(TokenTypes::T_PHP_OPEN_TAG, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo "baz";', 1),
            new Token(TokenTypes::T_PHP_CLOSE_TAG, '?>', 1)
        ];
        $this->assertEquals(
            $expectedOutput,
            $this->lexer->lex($this->template, '<?php echo "foo"; ?><% show("bar") %><?php echo "baz"; ?>')
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
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, '\\\\<% foo %>'));
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
            $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, $text));
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
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, sprintf($text, '<%', '%>')));
        $expectedOutput = [
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 2),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 3)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, sprintf($text, '{{', '}}')));
        $expectedOutput = [
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo', 2),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 3)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, sprintf($text, '{{!', '!}}')));
    }

    /**
     * Tests multiple lines of PHP
     */
    public function testLexingMultipleLinesOfPHP()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_PHP_OPEN_TAG, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo "<%";', 2),
            new Token(TokenTypes::T_PHP_CLOSE_TAG, '?>', 3)
        ];
        $text = '<?php' . PHP_EOL . ' echo "<%"; ' . PHP_EOL . '?>';
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, $text));
    }

    /**
     * Tests that native PHP functions are lexed inside statements
     */
    public function testLexingNativePHPFunctionsInsideStatements()
    {
        $text = '%s foo("bar") %s';
        $expectedOutput = [
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo("bar")', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, sprintf($text, '{{', '}}')));
        $expectedOutput = [
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo("bar")', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, sprintf($text, '{{!', '!}}')));
    }

    /**
     * Tests lexing neighboring escaped statements
     */
    public function testLexingNeighboringEscapedStatements()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_EXPRESSION, '{{foo}}{{!bar!}}<%baz%>', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, '\{{foo}}\{{!bar!}}\<%baz%>'));
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
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, '{{foo}}{{!bar!}}<%baz%>blah'));
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
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, 'a{{foo}}b{{!bar!}}c<%baz%>d'));
    }

    /**
     * Tests lexing nested functions
     */
    public function testLexingNestedFunctions()
    {
        // Nest PHP function in PHP function
        $expectedOutput = [
            new Token(TokenTypes::T_DIRECTIVE_OPEN, '<%', 1),
            new Token(TokenTypes::T_DIRECTIVE_NAME, 'show', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo(bar("baz"))', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, '<% show(foo(bar("baz"))) %>'));
        $text = '%s foo(bar("baz")) %s';
        $expectedOutput = [
            new Token(TokenTypes::T_SANITIZED_TAG_OPEN, '{{', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo(bar("baz"))', 1),
            new Token(TokenTypes::T_SANITIZED_TAG_CLOSE, '}}', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, sprintf($text, '{{', '}}')));
        $expectedOutput = [
            new Token(TokenTypes::T_UNSANITIZED_TAG_OPEN, '{{!', 1),
            new Token(TokenTypes::T_EXPRESSION, 'foo(bar("baz"))', 1),
            new Token(TokenTypes::T_UNSANITIZED_TAG_CLOSE, '!}}', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, sprintf($text, '{{!', '!}}')));
    }

    /**
     * Tests lexing PHP
     */
    public function testLexingPHP()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_PHP_OPEN_TAG, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo "foo";', 1),
            new Token(TokenTypes::T_PHP_CLOSE_TAG, '?>', 1)
        ];
        // Lex with long open tag and close tag
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, '<?php echo "foo"; ?>'));
        // Lex with long open tag and without close tag
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, '<?php echo "foo";'));
        // Lex with short open tag and close tag
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, '<? echo "foo"; ?>'));
        // Lex with short open tag and without close tag
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, '<? echo "foo";'));
    }

    /**
     * Tests lexing PHP without close tag
     */
    public function testLexingPHPWithoutCloseTag()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_PHP_OPEN_TAG, '<?php', 1),
            new Token(TokenTypes::T_EXPRESSION, 'echo 1;', 1),
            new Token(TokenTypes::T_PHP_CLOSE_TAG, '?>', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, '<?php echo 1;'));
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
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, '{{foo}}'));
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, '{{ foo }}'));
    }

    /**
     * Tests lexing statements whose delimiters are substrings of others
     */
    public function testLexingStatementsWhoseDelimitersAreSubstringsOfOthers()
    {
        $this->template->setDelimiters(Template::DELIMITER_TYPE_DIRECTIVE, ["{{{", "}}}"]);
        $this->template->setDelimiters(Template::DELIMITER_TYPE_SANITIZED_TAG, ["{{", "}}"]);
        $this->template->setDelimiters(Template::DELIMITER_TYPE_UNSANITIZED_TAG, ["{{!", "!}}"]);
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
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, '{{{foo}}}{{bar}}{{!baz!}}'));
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
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, '{{!foo!}}{{bar}}{{{baz}}}'));
    }

    /**
     * Tests that a text token is created
     */
    public function testLexingText()
    {
        $expectedOutput = [
            new Token(TokenTypes::T_EXPRESSION, 'foo', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, 'foo'));
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
            new Token(TokenTypes::T_EXPRESSION, '"bar"', 1),
            new Token(TokenTypes::T_DIRECTIVE_CLOSE, '%>', 1),
            new Token(TokenTypes::T_EXPRESSION, 'baz', 1)
        ];
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, 'foo<% show("bar") %>baz'));
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
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, '{{!foo!}}'));
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, '{{! foo !}}'));
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
            new Token(TokenTypes::T_EXPRESSION, 'foo', 5),
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
        $this->assertEquals($expectedOutput, $this->lexer->lex($this->template, $text));
    }
}
<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the Fortune compiler
 */
namespace Opulence\Views\Compilers\Fortune;
use InvalidArgumentException;
use Opulence\Views\Filters\XSSFilter;
use Opulence\Views\Compilers\Fortune\Parsers\AbstractSyntaxTree;
use Opulence\Views\Compilers\Fortune\Lexers\ILexer;
use Opulence\Views\Compilers\Fortune\Parsers\IParser;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\DirectiveNameNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\DirectiveNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\ExpressionNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\Node;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\SanitizedTagNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\UnsanitizedTagNode;
use Opulence\Views\IFortuneView;
use RuntimeException;

class FortuneCompilerTest extends \PHPUnit_Framework_TestCase
{
    /** @var FortuneCompiler The compiler to use in tests */
    private $compiler = null;
    /** @var ILexer|\PHPUnit_Framework_MockObject_MockObject The lexer to use in tests */
    private $lexer = null;
    /** @var IParser|\PHPUnit_Framework_MockObject_MockObject The parser to use in tests */
    private $parser = null;
    /** @var AbstractSyntaxTree The AST to use in tests */
    private $ast = null;
    /** @var IFortuneView|\PHPUnit_Framework_MockObject_MockObject The view to use in tests */
    private $view = null;
    /** @var XSSFilter The filter to use in tests */
    private $xssFilter = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->lexer = $this->getMock(ILexer::class);
        $this->parser = $this->getMock(IParser::class);
        $this->xssFilter = new XSSFilter();
        $this->compiler = new FortuneCompiler($this->lexer, $this->parser, $this->xssFilter);
        $this->ast = new AbstractSyntaxTree();
        $this->lexer->expects($this->any())->method("lex")->willReturn([]);
        $this->parser->expects($this->any())->method("parse")->willReturn($this->ast);
        $this->view = $this->getMock(IFortuneView::class);
    }

    /**
     * Tests appending text
     */
    public function testAppendingText()
    {
        $this->ast->getCurrentNode()
            ->addChild(new ExpressionNode("foo"));
        $this->compiler->append("bar");
        $this->assertEquals(
            "foo" . PHP_EOL . "bar",
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests calling a view function that takes no parameters
     */
    public function testCallingViewFunctionThatTakesNoParameters()
    {
        $this->compiler->registerViewFunction("foo", function ()
        {
            return "foobar";
        });
        $this->assertEquals("foobar", $this->compiler->callViewFunction("foo"));
    }

    /**
     * Tests calling a view function that takes parameters
     */
    public function testCallingViewFunctionThatTakesParameters()
    {
        $this->compiler->registerViewFunction("foo", function ($input)
        {
            return "foo" . $input;
        });
        $this->assertEquals("foobar", $this->compiler->callViewFunction("foo", "bar"));
    }

    /**
     * Tests compiling an expression
     */
    public function testCompilingExpression()
    {
        $tagNode = new ExpressionNode('<?php echo "foo"; ?>');
        $this->ast->getCurrentNode()
            ->addChild($tagNode);
        $this->assertEquals(
            '<?php echo "foo"; ?>',
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling a sanitized tag whose contents are an expression
     */
    public function testCompilingSanitizedTagWhoseContentsAreExpression()
    {
        $tagNode = new SanitizedTagNode('$foo ? "bar" : "baz"');
        $this->ast->getCurrentNode()
            ->addChild($tagNode);
        $this->assertEquals(
            '<?php echo $__opulenceFortuneCompiler->sanitize($foo ? "bar" : "baz"); ?>',
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling an unsanitized tag whose contents are an expression
     */
    public function testCompilingUnsanitizedTagWhoseContentsAreExpression()
    {
        $tagNode = new UnsanitizedTagNode('$foo ? "bar" : "baz"');
        $this->ast->getCurrentNode()
            ->addChild($tagNode);
        $this->assertEquals(
            '<?php echo $foo ? "bar" : "baz"; ?>',
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling view functions
     */
    public function testCompilingViewFunctions()
    {
        $this->compiler->registerDirectiveCompiler("foo", function ($expression)
        {
            return $expression;
        });
        $expressionContent = sprintf(
            'blah $__opulenceFortuneCompiler->callViewFunction("%s", date(%s)) baz',
            'bar',
            '$__opulenceFortuneCompiler->callViewFunction("myDate", \'Y\')'
        );

        // Test compiling in a directive
        $node = new DirectiveNode();
        $node->addChild(new DirectiveNameNode("foo"));
        $node->addChild(new ExpressionNode("blah bar(date(myDate('Y'))) baz"));
        $this->ast->getCurrentNode()
            ->addChild($node);
        $this->assertEquals(
            $expressionContent,
            $this->compiler->compile($this->view, $this->view->getContents())
        );

        // Test compiling in a sanitized tag
        $this->ast->clearNodes();
        $node = new SanitizedTagNode("blah bar(date(myDate('Y'))) baz");
        $this->ast->getCurrentNode()
            ->addChild($node);
        $this->assertEquals(
            '<?php echo $__opulenceFortuneCompiler->sanitize(' . $expressionContent . '); ?>',
            $this->compiler->compile($this->view, $this->view->getContents())
        );

        // Test compiling in an unsanitized tag
        $this->ast->clearNodes();
        $node = new UnsanitizedTagNode("blah bar(date(myDate('Y'))) baz");
        $this->ast->getCurrentNode()
            ->addChild($node);
        $this->assertEquals(
            '<?php echo ' . $expressionContent . '; ?>',
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests defining a part
     */
    public function testDefiningPart()
    {
        $this->compiler->startPart("foo");
        echo "bar";
        $this->compiler->endPart();
        $this->assertEquals("bar", $this->compiler->showPart("foo"));
    }

    /**
     * Tests that an exception is thrown with an invalid node type
     */
    public function testExceptionIsThrownWithInvalidNodeType()
    {
        $this->setExpectedException(RuntimeException::class);
        $this->ast->getCurrentNode()
            ->addChild($this->getMockForAbstractClass(Node::class, [], "FakeNode"));
        $this->compiler->compile($this->view, $this->view->getContents());
    }

    /**
     * Tests that an exception is thrown with an invalid view function name
     */
    public function testExceptionThrownWithInvalidViewFunctionName()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->assertEquals("foobar", $this->compiler->callViewFunction("foo"));
    }

    /**
     * Tests registering a directive compiler
     */
    public function testRegisteringDirectiveCompiler()
    {
        $this->compiler->registerDirectiveCompiler("foo", function ($expression)
        {
            return "<?php foo $expression ?>";
        });
        $directiveNode = new DirectiveNode();
        $directiveNode->addChild(new DirectiveNameNode("foo"));
        $directiveNode->addChild(new ExpressionNode("bar"));
        $this->ast->getCurrentNode()
            ->addChild($directiveNode);
        $this->assertEquals(
            '<?php foo bar ?>',
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests sanitizing text
     */
    public function testSanitizingText()
    {
        $text = 'A&W"\'';
        $this->assertEquals($this->xssFilter->run($text), $this->compiler->sanitize($text));
    }

    /**
     * Tests showing a parent part
     */
    public function testShowingParentPart()
    {
        $this->compiler->startPart("foo");
        echo "__opulenceParentPlaceholder";
        echo "baz";
        $this->compiler->endPart();
        $this->compiler->startPart("foo");
        echo "bar";
        $this->compiler->endPart();
        $this->assertEquals("barbaz", $this->compiler->showPart("foo"));
    }

    /**
     * Tests showing parts from three generations of parents
     */
    public function testShowingPartsFromThreeGenerationsOfParents()
    {
        $this->compiler->startPart("foo");
        echo "__opulenceParentPlaceholder";
        echo "baz";
        $this->compiler->endPart();
        $this->compiler->startPart("foo");
        echo "__opulenceParentPlaceholder";
        echo "bar";
        $this->compiler->endPart();
        $this->compiler->startPart("foo");
        echo "blah";
        $this->compiler->endPart();
        $this->assertEquals("blahbarbaz", $this->compiler->showPart("foo"));
    }
}
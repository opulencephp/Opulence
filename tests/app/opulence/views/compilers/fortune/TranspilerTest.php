<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the Fortune transpiler
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
use Opulence\Views\IView;
use Opulence\Views\View;
use RuntimeException;

class TranspilerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Transpiler The transpiler to use in tests */
    private $transpiler = null;
    /** @var ILexer|\PHPUnit_Framework_MockObject_MockObject The lexer to use in tests */
    private $lexer = null;
    /** @var IParser|\PHPUnit_Framework_MockObject_MockObject The parser to use in tests */
    private $parser = null;
    /** @var AbstractSyntaxTree The AST to use in tests */
    private $ast = null;
    /** @var IView|\PHPUnit_Framework_MockObject_MockObject The view to use in tests */
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
        $this->transpiler = new Transpiler($this->lexer, $this->parser, $this->xssFilter);
        $this->ast = new AbstractSyntaxTree();
        $this->lexer->expects($this->any())->method("lex")->willReturn([]);
        $this->parser->expects($this->any())->method("parse")->willReturn($this->ast);
        $this->view = $this->getMock(IView::class);
    }

    /**
     * Tests that adjacent expressions have spaces between them
     */
    public function testAdjacentExpressionHaveSpacesBetweenThem()
    {
        $this->ast->getCurrentNode()
            ->addChild(new ExpressionNode('<?php'))
            ->addChild(new ExpressionNode('echo "foo";'))
            ->addChild(new ExpressionNode('?>'));
        $this->assertEquals(
            '<?php echo "foo"; ?>',
            $this->transpiler->transpile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests calling a view function that takes no parameters
     */
    public function testCallingViewFunctionThatTakesNoParameters()
    {
        $this->transpiler->registerViewFunction("foo", function ()
        {
            return "foobar";
        });
        $this->assertEquals("foobar", $this->transpiler->callViewFunction("foo"));
    }

    /**
     * Tests calling a view function that takes parameters
     */
    public function testCallingViewFunctionThatTakesParameters()
    {
        $this->transpiler->registerViewFunction("foo", function ($input)
        {
            return "foo" . $input;
        });
        $this->assertEquals("foobar", $this->transpiler->callViewFunction("foo", "bar"));
    }

    /**
     * Tests defining a part
     */
    public function testDefiningPart()
    {
        $this->transpiler->startPart("foo");
        echo "bar";
        $this->transpiler->endPart();
        $this->assertEquals("bar", $this->transpiler->showPart("foo"));
    }

    /**
     * Tests that an exception is thrown with an invalid node type
     */
    public function testExceptionIsThrownWithInvalidNodeType()
    {
        $this->setExpectedException(RuntimeException::class);
        /** @var Node|\PHPUnit_Framework_MockObject_MockObject $invalidNode */
        $invalidNode = $this->getMockForAbstractClass(Node::class, [], "FakeNode");
        $this->ast->getCurrentNode()
            ->addChild($invalidNode);
        $this->transpiler->transpile($this->view, $this->view->getContents());
    }

    /**
     * Tests that an exception is thrown when there's no transpiler for a directive
     */
    public function testExceptionThrownWhenNoTranspilerRegisteredForDirective()
    {
        $this->setExpectedException(RuntimeException::class);
        $directiveNode = new DirectiveNode();
        $directiveNode->addChild(new DirectiveNameNode("foo"));
        $directiveNode->addChild(new ExpressionNode("bar"));
        $this->ast->getCurrentNode()
            ->addChild($directiveNode);
        $this->transpiler->transpile($this->view, $this->view->getContents());
    }

    /**
     * Tests that an exception is thrown with an invalid view function name
     */
    public function testExceptionThrownWithInvalidViewFunctionName()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->transpiler->callViewFunction("foo");
    }

    /**
     * Tests that the first value of a variable that is inherited twice is used
     */
    public function testFirstValueOfVariableThatIsInheritedTwiceIsUsed()
    {
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $parent1 */
        $parent1 = $this->getMock(IView::class);
        $parent1->expects($this->once())
            ->method("getVars")
            ->willReturn(["foo" => "bar"]);
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $parent2 */
        $parent2 = $this->getMock(IView::class);
        $parent2->expects($this->once())
            ->method("getVars")
            ->willReturn(["foo" => "baz"]);
        $child = new View();
        $this->transpiler->addParent($parent1, $child);
        $this->transpiler->addParent($parent2, $child);
        $this->transpiler->transpile($child, "");
        $this->assertEquals("bar", $child->getVar("foo"));
    }

    /**
     * Tests passing a variable that was already defined
     */
    public function testPassingVariableThatWasAlreadyDefined()
    {
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $parent1 */
        $parent1 = $this->getMock(IView::class);
        $parent1->expects($this->once())
            ->method("getVars")
            ->willReturn(["foo" => "bar"]);
        $this->view->expects($this->never())
            ->method("setVar");
        $this->view->expects($this->once())
            ->method("hasVar")
            ->with("foo")
            ->willReturn(true);
        $this->transpiler->addParent($parent1, $this->view);
        $this->transpiler->transpile($this->view, $this->view->getContents());
    }

    /**
     * Tests passing a variable that was not defined
     */
    public function testPassingVariableThatWasNotDefined()
    {
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $parent1 */
        $parent1 = $this->getMock(IView::class);
        $parent1->expects($this->once())
            ->method("getVars")
            ->willReturn(["foo" => "bar"]);
        $this->view->expects($this->once())
            ->method("setVar")
            ->with("foo", "bar");
        $this->view->expects($this->once())
            ->method("hasVar")
            ->with("foo")
            ->willReturn(false);
        $this->transpiler->addParent($parent1, $this->view);
        $this->transpiler->transpile($this->view, $this->view->getContents());
    }

    /**
     * Tests registering a directive transpiler
     */
    public function testRegisteringDirectiveTranspiler()
    {
        $this->transpiler->registerDirectiveTranspiler("foo", function ($expression)
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
            $this->transpiler->transpile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests sanitizing text
     */
    public function testSanitizingText()
    {
        $text = 'A&W"\'';
        $this->assertEquals($this->xssFilter->run($text), $this->transpiler->sanitize($text));
    }

    /**
     * Tests showing a parent part
     */
    public function testShowingParentPart()
    {
        $this->transpiler->startPart("foo");
        echo "__opulenceParentPlaceholder";
        echo "baz";
        $this->transpiler->endPart();
        $this->transpiler->startPart("foo");
        echo "bar";
        $this->transpiler->endPart();
        $this->assertEquals("barbaz", $this->transpiler->showPart("foo"));
    }

    /**
     * Tests showing parts from three generations of parents
     */
    public function testShowingPartsFromThreeGenerationsOfParents()
    {
        $this->transpiler->startPart("foo");
        echo "__opulenceParentPlaceholder";
        echo "baz";
        $this->transpiler->endPart();
        $this->transpiler->startPart("foo");
        echo "__opulenceParentPlaceholder";
        echo "bar";
        $this->transpiler->endPart();
        $this->transpiler->startPart("foo");
        echo "blah";
        $this->transpiler->endPart();
        $this->assertEquals("blahbarbaz", $this->transpiler->showPart("foo"));
    }

    /**
     * Tests transpiling an expression
     */
    public function testTranspilingExpression()
    {
        $node = new ExpressionNode('<?php echo "foo"; ?>');
        $this->ast->getCurrentNode()
            ->addChild($node);
        $this->assertEquals(
            '<?php echo "foo"; ?>',
            $this->transpiler->transpile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests transpiling a sanitized tag whose contents are an expression
     */
    public function testTranspilingSanitizedTagWhoseContentsAreExpression()
    {
        $tagNode = new SanitizedTagNode();
        $tagNode->addChild(new ExpressionNode('$foo ? "bar" : "baz"'));
        $this->ast->getCurrentNode()
            ->addChild($tagNode);
        $this->assertEquals(
            '<?php echo $__opulenceFortuneTranspiler->sanitize($foo ? "bar" : "baz"); ?>',
            $this->transpiler->transpile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests transpiling an unsanitized tag whose contents are an expression
     */
    public function testTranspilingUnsanitizedTagWhoseContentsAreExpression()
    {
        $tagNode = new UnsanitizedTagNode();
        $tagNode->addChild(new ExpressionNode('$foo ? "bar" : "baz"'));
        $this->ast->getCurrentNode()
            ->addChild($tagNode);
        $this->assertEquals(
            '<?php echo $foo ? "bar" : "baz"; ?>',
            $this->transpiler->transpile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests transpiling view functions
     */
    public function testTranspilingViewFunctions()
    {
        $this->transpiler->registerDirectiveTranspiler("foo", function ($expression)
        {
            return $expression;
        });
        $expressionContent = sprintf(
            'blah $__opulenceFortuneTranspiler->callViewFunction("%s", date(%s)) %s',
            'bar',
            '$__opulenceFortuneTranspiler->callViewFunction("myDate", \'Y\')',
            '$__opulenceFortuneTranspiler->callViewFunction("baz")'
        );

        // Test transpiling in a directive
        $node = new DirectiveNode();
        $node->addChild(new DirectiveNameNode("foo"));
        $node->addChild(new ExpressionNode("blah bar(date(myDate('Y'))) baz()"));
        $this->ast->getCurrentNode()
            ->addChild($node);
        $this->assertEquals(
            $expressionContent,
            $this->transpiler->transpile($this->view, $this->view->getContents())
        );

        // Test transpiling in a sanitized tag
        $this->ast->clearNodes();
        $node = new SanitizedTagNode();
        $node->addChild(new ExpressionNode("blah bar(date(myDate('Y'))) baz()"));
        $this->ast->getCurrentNode()
            ->addChild($node);
        $this->assertEquals(
            '<?php echo $__opulenceFortuneTranspiler->sanitize(' . $expressionContent . '); ?>',
            $this->transpiler->transpile($this->view, $this->view->getContents())
        );

        // Test transpiling in an unsanitized tag
        $this->ast->clearNodes();
        $node = new UnsanitizedTagNode();
        $node->addChild(new ExpressionNode("blah bar(date(myDate('Y'))) baz()"));
        $this->ast->getCurrentNode()
            ->addChild($node);
        $this->assertEquals(
            '<?php echo ' . $expressionContent . '; ?>',
            $this->transpiler->transpile($this->view, $this->view->getContents())
        );
    }
}
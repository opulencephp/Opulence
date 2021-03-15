<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Views\Tests\Compilers\Fortune;

use InvalidArgumentException;
use Opulence\Views\Caching\ICache;
use Opulence\Views\Compilers\Fortune\Lexers\ILexer;
use Opulence\Views\Compilers\Fortune\Parsers\AbstractSyntaxTree;
use Opulence\Views\Compilers\Fortune\Parsers\IParser;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\CommentNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\DirectiveNameNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\DirectiveNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\ExpressionNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\Node;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\SanitizedTagNode;
use Opulence\Views\Compilers\Fortune\Parsers\Nodes\UnsanitizedTagNode;
use Opulence\Views\Compilers\Fortune\Transpiler;
use Opulence\Views\Filters\XssFilter;
use Opulence\Views\IView;
use Opulence\Views\View;
use RuntimeException;

/**
 * Tests the Fortune transpiler
 */
class TranspilerTest extends \PHPUnit\Framework\TestCase
{
    /** @var Transpiler The transpiler to use in tests */
    private $transpiler = null;
    /** @var ILexer|\PHPUnit_Framework_MockObject_MockObject The lexer to use in tests */
    private $lexer = null;
    /** @var IParser|\PHPUnit_Framework_MockObject_MockObject The parser to use in tests */
    private $parser = null;
    /** @var AbstractSyntaxTree The AST to use in tests */
    private $ast = null;
    /** @var ICache|\PHPUnit_Framework_MockObject_MockObject The view cache to use in tests */
    private $cache = null;
    /** @var IView|\PHPUnit_Framework_MockObject_MockObject The view to use in tests */
    private $view = null;
    /** @var XssFilter The filter to use in tests */
    private $xssFilter = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->lexer = $this->createMock(ILexer::class);
        $this->parser = $this->createMock(IParser::class);
        $this->cache = $this->createMock(ICache::class);
        $this->xssFilter = new XssFilter();
        $this->transpiler = new Transpiler($this->lexer, $this->parser, $this->cache, $this->xssFilter);
        $this->ast = new AbstractSyntaxTree();
        $this->lexer->expects($this->any())->method('lex')->willReturn([]);
        $this->parser->expects($this->any())->method('parse')->willReturn($this->ast);
        $this->view = $this->createMock(IView::class);
        $this->view->expects($this->any())
            ->method('getVars')
            ->willReturn([]);
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
            $this->transpiler->transpile($this->view)
        );
    }

    /**
     * Tests that cache is used when it has a view
     */
    public function testCacheIsUsedWhenItHasView()
    {
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $view */
        $view = $this->createMock(IView::class);
        $view->expects($this->any())
            ->method('getContents')
            ->willReturn('foo');
        $view->expects($this->any())
            ->method('getVars')
            ->willReturn(['bar' => 'baz']);
        $this->cache->expects($this->once())
            ->method('get')
            ->with($view)
            ->willReturn('transpiled');
        $this->assertEquals('transpiled', $this->transpiler->transpile($view));
    }

    /**
     * Tests calling a view function that takes no parameters
     */
    public function testCallingViewFunctionThatTakesNoParameters()
    {
        $this->transpiler->registerViewFunction('foo', function () {
            return 'foobar';
        });
        $this->assertEquals('foobar', $this->transpiler->callViewFunction('foo'));
    }

    /**
     * Tests calling a view function that takes parameters
     */
    public function testCallingViewFunctionThatTakesParameters()
    {
        $this->transpiler->registerViewFunction('foo', function ($input) {
            return 'foo' . $input;
        });
        $this->assertEquals('foobar', $this->transpiler->callViewFunction('foo', 'bar'));
    }

    /**
     * Tests defining a part
     */
    public function testDefiningPart()
    {
        $this->transpiler->startPart('foo');
        echo 'bar';
        $this->transpiler->endPart();
        $this->assertEquals('bar', $this->transpiler->showPart('foo'));
    }

    /**
     * Tests that an exception is thrown with an invalid node type
     */
    public function testExceptionIsThrownWithInvalidNodeType()
    {
        $this->expectException(RuntimeException::class);
        /** @var Node|\PHPUnit_Framework_MockObject_MockObject $invalidNode */
        $invalidNode = $this->getMockForAbstractClass(Node::class, [], 'FakeNode');
        $this->ast->getCurrentNode()
            ->addChild($invalidNode);
        $this->transpiler->transpile($this->view);
    }

    /**
     * Tests that an exception is thrown when there's no transpiler for a directive
     */
    public function testExceptionThrownWhenNoTranspilerRegisteredForDirective()
    {
        $this->expectException(RuntimeException::class);
        $directiveNode = new DirectiveNode();
        $directiveNode->addChild(new DirectiveNameNode('foo'));
        $directiveNode->addChild(new ExpressionNode('bar'));
        $this->ast->getCurrentNode()
            ->addChild($directiveNode);
        $this->transpiler->transpile($this->view);
    }

    /**
     * Tests that an exception is thrown with an invalid view function name
     */
    public function testExceptionThrownWithInvalidViewFunctionName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->transpiler->callViewFunction('foo');
    }

    /**
     * Tests that the first value of a variable that is inherited twice is used
     */
    public function testFirstValueOfVariableThatIsInheritedTwiceIsUsed()
    {
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $parent1 */
        $parent1 = $this->createMock(IView::class);
        $parent1->expects($this->once())
            ->method('getVars')
            ->willReturn(['foo' => 'bar']);
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $parent2 */
        $parent2 = $this->createMock(IView::class);
        $parent2->expects($this->once())
            ->method('getVars')
            ->willReturn(['foo' => 'baz']);
        $child = new View();
        $this->transpiler->addParent($parent1, $child);
        $this->transpiler->addParent($parent2, $child);
        $this->transpiler->transpile($child);
        $this->assertEquals('bar', $child->getVar('foo'));
    }

    /**
     * Tests passing a variable that was already defined
     */
    public function testPassingVariableThatWasAlreadyDefined()
    {
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $parent1 */
        $parent1 = $this->createMock(IView::class);
        $parent1->expects($this->once())
            ->method('getVars')
            ->willReturn(['foo' => 'bar']);
        $this->view->expects($this->never())
            ->method('setVar');
        $this->view->expects($this->once())
            ->method('hasVar')
            ->with('foo')
            ->willReturn(true);
        $this->transpiler->addParent($parent1, $this->view);
        $this->transpiler->transpile($this->view);
    }

    /**
     * Tests passing a variable that was not defined
     */
    public function testPassingVariableThatWasNotDefined()
    {
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $parent1 */
        $parent1 = $this->createMock(IView::class);
        $parent1->expects($this->once())
            ->method('getVars')
            ->willReturn(['foo' => 'bar']);
        $this->view->expects($this->once())
            ->method('setVar')
            ->with('foo', 'bar');
        $this->view->expects($this->once())
            ->method('hasVar')
            ->with('foo')
            ->willReturn(false);
        $this->transpiler->addParent($parent1, $this->view);
        $this->transpiler->transpile($this->view);
    }

    /**
     * Tests registering a directive transpiler
     */
    public function testRegisteringDirectiveTranspiler()
    {
        $this->transpiler->registerDirectiveTranspiler('foo', function ($expression) {
            return "<?php foo $expression ?>";
        });
        $directiveNode = new DirectiveNode();
        $directiveNode->addChild(new DirectiveNameNode('foo'));
        $directiveNode->addChild(new ExpressionNode('bar'));
        $this->ast->getCurrentNode()
            ->addChild($directiveNode);
        $this->assertEquals(
            '<?php foo bar ?>',
            $this->transpiler->transpile($this->view)
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
     * Tests showing latest part
     */
    public function testShowingLatestPart()
    {
        $this->transpiler->startPart('foo');
        echo 'bar';
        $this->assertEquals('bar', $this->transpiler->showPart());
    }

    /**
     * Tests showing a parent part
     */
    public function testShowingParentPart()
    {
        $this->transpiler->startPart('foo');
        echo '__opulenceParentPlaceholder';
        echo 'baz';
        $this->transpiler->endPart();
        $this->transpiler->startPart('foo');
        echo 'bar';
        $this->transpiler->endPart();
        $this->assertEquals('barbaz', $this->transpiler->showPart('foo'));
    }

    /**
     * Tests showing parts from three generations of parents
     */
    public function testShowingPartsFromThreeGenerationsOfParents()
    {
        $this->transpiler->startPart('foo');
        echo '__opulenceParentPlaceholder';
        echo 'baz';
        $this->transpiler->endPart();
        $this->transpiler->startPart('foo');
        echo '__opulenceParentPlaceholder';
        echo 'bar';
        $this->transpiler->endPart();
        $this->transpiler->startPart('foo');
        echo 'blah';
        $this->transpiler->endPart();
        $this->assertEquals('blahbarbaz', $this->transpiler->showPart('foo'));
    }

    /**
     * Tests that the transpiled contents are cached
     */
    public function testTranspiledContentsAreCached()
    {
        /** @var IView|\PHPUnit_Framework_MockObject_MockObject $view */
        $view = $this->createMock(IView::class);
        $view->expects($this->any())
            ->method('getContents')
            ->willReturn('foo');
        $view->expects($this->any())
            ->method('getVars')
            ->willReturn(['bar' => 'baz']);
        $this->cache->expects($this->once())
            ->method('get')
            ->willReturn(null);
        $this->cache->expects($this->once())
            ->method('set')
            ->with($view, '');
        $this->transpiler->transpile($view);
    }

    /**
     * Tests transpiling a comment whose contents are an expression
     */
    public function testTranspilingCommentWhoseContentsAreExpression()
    {
        $commentNode = new CommentNode();
        $commentNode->addChild(new ExpressionNode('This is my comment'));
        $this->ast->getCurrentNode()
            ->addChild($commentNode);
        $this->assertEquals(
            '<?php /* This is my comment */ ?>',
            $this->transpiler->transpile($this->view)
        );
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
            $this->transpiler->transpile($this->view)
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
            $this->transpiler->transpile($this->view)
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
            $this->transpiler->transpile($this->view)
        );
    }
}

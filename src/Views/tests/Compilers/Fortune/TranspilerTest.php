<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Tests the Fortune transpiler
 */
class TranspilerTest extends TestCase
{
    private Transpiler $transpiler;
    /** @var ILexer|MockObject The lexer to use in tests */
    private ILexer $lexer;
    /** @var IParser|MockObject The parser to use in tests */
    private IParser $parser;
    private AbstractSyntaxTree $ast;
    /** @var ICache|MockObject The view cache to use in tests */
    private ICache $cache;
    /** @var IView|MockObject The view to use in tests */
    private IView $view;
    private XssFilter $xssFilter;

    protected function setUp(): void
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

    public function testAdjacentExpressionHaveSpacesBetweenThem(): void
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

    public function testCacheIsUsedWhenItHasView(): void
    {
        /** @var IView|MockObject $view */
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

    public function testCallingViewFunctionThatTakesNoParameters(): void
    {
        $this->transpiler->registerViewFunction('foo', fn () => 'foobar');
        $this->assertEquals('foobar', $this->transpiler->callViewFunction('foo'));
    }

    public function testCallingViewFunctionThatTakesParameters(): void
    {
        $this->transpiler->registerViewFunction('foo', fn ($input) => 'foo' . $input);
        $this->assertEquals('foobar', $this->transpiler->callViewFunction('foo', 'bar'));
    }

    public function testDefiningPart(): void
    {
        $this->transpiler->startPart('foo');
        echo 'bar';
        $this->transpiler->endPart();
        $this->assertEquals('bar', $this->transpiler->showPart('foo'));
    }

    public function testExceptionIsThrownWithInvalidNodeType(): void
    {
        $this->expectException(RuntimeException::class);
        /** @var Node|MockObject $invalidNode */
        $invalidNode = $this->getMockForAbstractClass(Node::class, [], 'FakeNode');
        $this->ast->getCurrentNode()
            ->addChild($invalidNode);
        $this->transpiler->transpile($this->view);
    }

    public function testExceptionThrownWhenNoTranspilerRegisteredForDirective(): void
    {
        $this->expectException(RuntimeException::class);
        $directiveNode = new DirectiveNode();
        $directiveNode->addChild(new DirectiveNameNode('foo'));
        $directiveNode->addChild(new ExpressionNode('bar'));
        $this->ast->getCurrentNode()
            ->addChild($directiveNode);
        $this->transpiler->transpile($this->view);
    }

    public function testExceptionThrownWithInvalidViewFunctionName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->transpiler->callViewFunction('foo');
    }

    public function testFirstValueOfVariableThatIsInheritedTwiceIsUsed(): void
    {
        /** @var IView|MockObject $parent1 */
        $parent1 = $this->createMock(IView::class);
        $parent1->expects($this->once())
            ->method('getVars')
            ->willReturn(['foo' => 'bar']);
        /** @var IView|MockObject $parent2 */
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

    public function testPassingVariableThatWasAlreadyDefined(): void
    {
        /** @var IView|MockObject $parent1 */
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

    public function testPassingVariableThatWasNotDefined(): void
    {
        /** @var IView|MockObject $parent1 */
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

    public function testRegisteringDirectiveTranspiler(): void
    {
        $this->transpiler->registerDirectiveTranspiler('foo', fn ($expression) => "<?php foo $expression ?>");
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

    public function testSanitizingText(): void
    {
        $text = 'A&W"\'';
        $this->assertEquals($this->xssFilter->run($text), $this->transpiler->sanitize($text));
    }

    public function testShowingLatestPart(): void
    {
        $this->transpiler->startPart('foo');
        echo 'bar';
        $this->assertEquals('bar', $this->transpiler->showPart());
    }

    public function testShowingParentPart(): void
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

    public function testShowingPartsFromThreeGenerationsOfParents(): void
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

    public function testTranspiledContentsAreCached(): void
    {
        /** @var IView|MockObject $view */
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

    public function testTranspilingCommentWhoseContentsAreExpression(): void
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

    public function testTranspilingExpression(): void
    {
        $node = new ExpressionNode('<?php echo "foo"; ?>');
        $this->ast->getCurrentNode()
            ->addChild($node);
        $this->assertEquals(
            '<?php echo "foo"; ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    public function testTranspilingSanitizedTagWhoseContentsAreExpression(): void
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

    public function testTranspilingUnsanitizedTagWhoseContentsAreExpression(): void
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

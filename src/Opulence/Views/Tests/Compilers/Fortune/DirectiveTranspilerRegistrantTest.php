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

use Opulence\Views\Caching\ICache;
use Opulence\Views\Compilers\Fortune\DirectiveTranspilerRegistrant;
use Opulence\Views\Compilers\Fortune\Lexers\Lexer;
use Opulence\Views\Compilers\Fortune\Parsers\Parser;
use Opulence\Views\Compilers\Fortune\Transpiler;
use Opulence\Views\Filters\XssFilter;
use Opulence\Views\View;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Test the Fortune directive transpiler registrant
 */
class DirectiveTranspilerRegistrantTest extends \PHPUnit\Framework\TestCase
{
    private DirectiveTranspilerRegistrant $registrant;
    private Transpiler $transpiler;
    private View $view;

    protected function setUp(): void
    {
        $this->view = new View();
        $this->registrant = new DirectiveTranspilerRegistrant();
        /** @var ICache|MockObject $cache */
        $cache = $this->createMock(ICache::class);
        $cache->expects($this->any())
            ->method('has')
            ->willReturn(false);
        $this->transpiler = new Transpiler(new Lexer(), new Parser(), $cache, new XssFilter());
    }

    public function testTranspilingElse(): void
    {
        $this->view->setContents('<% else %>');
        $this->assertEquals(
            '<?php else: ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    /**
     * Tests transpiling an else-if directive
     */
    public function testTranspilingElseIf(): void
    {
        $this->view->setContents('<% elseif(true) %>');
        $this->assertEquals(
            '<?php elseif(true): ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    /**
     * Tests transpiling an end-for directive
     */
    public function testTranspilingEndFor(): void
    {
        $this->view->setContents('<% endfor %>');
        $this->assertEquals(
            '<?php endfor; ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    /**
     * Tests transpiling an end-foreach directive
     */
    public function testTranspilingEndForeach(): void
    {
        $this->view->setContents('<% endforeach %>');
        $this->assertEquals(
            '<?php endforeach; ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    /**
     * Tests transpiling an end-if directive
     */
    public function testTranspilingEndIf(): void
    {
        $this->view->setContents('<% endif %>');
        $this->assertEquals(
            '<?php endif; ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    /**
     * Tests transpiling an end-part directive
     */
    public function testTranspilingEndPart(): void
    {
        $this->view->setContents('<% endpart %>');
        $this->assertEquals(
            '<?php $__opulenceFortuneTranspiler->endPart(); ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    /**
     * Tests transpiling an end-while directive
     */
    public function testTranspilingEndWhile(): void
    {
        $this->view->setContents('<% endwhile %>');
        $this->assertEquals(
            '<?php endwhile; ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    public function testTranspilingExtend(): void
    {
        $this->view->setContents('<% extends("foo.php") %>bar');
        $expected = [
            '<?php $__opulenceViewParent = $__opulenceViewFactory->createView("foo.php");$__opulenceFortuneTranspiler->addParent($__opulenceViewParent, $__opulenceView);extract($__opulenceView->getVars()); ?>',
            '<?php $__opulenceParentContents = isset($__opulenceParentContents) ? $__opulenceParentContents : [];$__opulenceParentContents[] = $__opulenceFortuneTranspiler->transpile($__opulenceViewParent); ?>',
            'bar',
            '<?php echo eval("?>" . array_shift($__opulenceParentContents)); ?>'
        ];
        $this->assertEquals(
            implode(PHP_EOL, $expected),
            $this->transpiler->transpile($this->view)
        );
    }

    public function testTranspilingFor(): void
    {
        $this->view->setContents('<% for($i=0;$i<10;$i++) %>');
        $this->assertEquals(
            '<?php for($i=0;$i<10;$i++): ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    /**
     * Tests transpiling a for-else directive
     */
    public function testTranspilingForElse(): void
    {
        $this->view->setContents('<% forelse %>');
        $this->assertEquals(
            '<?php endforeach; if(array_pop($__opulenceForElseEmpty)): ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    /**
     * Tests transpiling a for-if directive
     */
    public function testTranspilingForIf(): void
    {
        $this->view->setContents('<% forif($foo as $bar) %>');
        $this->assertEquals(
            '<?php if(!isset($__opulenceForElseEmpty)): $__opulenceForElseEmpty = []; endif;$__opulenceForElseEmpty[] = true;' .
            'foreach($foo as $bar):' .
            '$__opulenceForElseEmpty[count($__opulenceForElseEmpty) - 1] = false; ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    public function testTranspilingForeach(): void
    {
        $this->view->setContents('<% foreach($foo as $bar) %>');
        $this->assertEquals(
            '<?php foreach($foo as $bar): ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    public function testTranspilingIf(): void
    {
        $this->view->setContents('<% if(true) %>');
        $this->assertEquals(
            '<?php if(true): ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    public function testTranspilingInclude(): void
    {
        $this->view->setContents('<% include("foo.php") %>bar');
        $code = '<?php call_user_func(function() use ($__opulenceViewFactory, $__opulenceFortuneTranspiler){';
        $code .= '$__opulenceIncludedView = $__opulenceViewFactory->createView("foo.php");';
        $code .= 'extract($__opulenceIncludedView->getVars());';
        $code .= 'if(count(func_get_arg(0)) > 0){extract(func_get_arg(0));}';
        $code .= 'eval("?>" . $__opulenceFortuneTranspiler->transpile($__opulenceIncludedView));';
        $code .= '}, []);';
        $code .= ' ?>';
        $this->assertEquals(
            "{$code}bar",
            $this->transpiler->transpile($this->view)
        );
    }

    public function testTranspilingIncludeWithPassedVariables(): void
    {
        $this->view->setContents('<% include("foo.php", ["foo" => "bar"]) %>baz');
        $code = '<?php call_user_func(function() use ($__opulenceViewFactory, $__opulenceFortuneTranspiler){';
        $code .= '$__opulenceIncludedView = $__opulenceViewFactory->createView("foo.php");';
        $code .= 'extract($__opulenceIncludedView->getVars());';
        $code .= 'if(count(func_get_arg(0)) > 0){extract(func_get_arg(0));}';
        $code .= 'eval("?>" . $__opulenceFortuneTranspiler->transpile($__opulenceIncludedView));';
        $code .= '}, ["foo" => "bar"]);';
        $code .= ' ?>';
        $this->assertEquals(
            "{$code}baz",
            $this->transpiler->transpile($this->view)
        );
    }

    public function testTranspilingIncludeWithPassedVariablesThatContainComma(): void
    {
        $this->view->setContents('<% include("foo.php", compact("foo", "bar")) %>baz');
        $code = '<?php call_user_func(function() use ($__opulenceViewFactory, $__opulenceFortuneTranspiler){';
        $code .= '$__opulenceIncludedView = $__opulenceViewFactory->createView("foo.php");';
        $code .= 'extract($__opulenceIncludedView->getVars());';
        $code .= 'if(count(func_get_arg(0)) > 0){extract(func_get_arg(0));}';
        $code .= 'eval("?>" . $__opulenceFortuneTranspiler->transpile($__opulenceIncludedView));';
        $code .= '}, compact("foo", "bar"));';
        $code .= ' ?>';
        $this->assertEquals(
            "{$code}baz",
            $this->transpiler->transpile($this->view)
        );
    }

    public function testTranspilingIncludeWithVariableViewNameAndPassedVariables(): void
    {
        $this->view->setContents('<% include($foo, ["foo" => "bar"]) %>baz');
        $code = '<?php call_user_func(function() use ($__opulenceViewFactory, $__opulenceFortuneTranspiler){';
        $code .= '$__opulenceIncludedView = $__opulenceViewFactory->createView($foo);';
        $code .= 'extract($__opulenceIncludedView->getVars());';
        $code .= 'if(count(func_get_arg(0)) > 0){extract(func_get_arg(0));}';
        $code .= 'eval("?>" . $__opulenceFortuneTranspiler->transpile($__opulenceIncludedView));';
        $code .= '}, ["foo" => "bar"]);';
        $code .= ' ?>';
        $this->assertEquals(
            "{$code}baz",
            $this->transpiler->transpile($this->view)
        );
    }

    public function testTranspilingParent(): void
    {
        $this->view->setContents('<% parent %>');
        $this->assertEquals(
            '__opulenceParentPlaceholder',
            $this->transpiler->transpile($this->view)
        );
    }

    public function testTranspilingPart(): void
    {
        $this->view->setContents('<% part("foo") %>');
        $this->assertEquals(
            '<?php $__opulenceFortuneTranspiler->startPart("foo"); ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    public function testTranspilingShow(): void
    {
        $this->view->setContents('<% show("foo") %>');
        $this->assertEquals(
            '<?php echo $__opulenceFortuneTranspiler->showPart("foo"); ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    public function testTranspilingShowWithoutExpression(): void
    {
        $this->view->setContents('<% show %>');
        $this->assertEquals(
            '<?php echo $__opulenceFortuneTranspiler->showPart(); ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    public function testTranspilingWhile(): void
    {
        $this->view->setContents('<% while(true) %>');
        $this->assertEquals(
            '<?php while(true): ?>',
            $this->transpiler->transpile($this->view)
        );
    }
}

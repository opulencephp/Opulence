<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Test the Fortune directive transpiler registrant
 */
namespace Opulence\Views\Compilers\Fortune;
use Opulence\Views\Caching\ICache;
use Opulence\Views\Filters\XSSFilter;
use Opulence\Views\Compilers\Fortune\Lexers\Lexer;
use Opulence\Views\Compilers\Fortune\Parsers\Parser;
use Opulence\Views\View;

class DirectiveTranspilerRegistrantTest extends \PHPUnit_Framework_TestCase
{
    /** @var DirectiveTranspilerRegistrant The registrant to use in tests */
    private $registrant = null;
    /** @var Transpiler The transpiler to use in tests */
    private $transpiler = null;
    /** @var View The view to use in tests */
    private $view = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->view = new View();
        $this->registrant = new DirectiveTranspilerRegistrant();
        /** @var ICache|\PHPUnit_Framework_MockObject_MockObject $cache */
        $cache = $this->getMock(ICache::class);
        $cache->expects($this->any())
            ->method("has")
            ->willReturn(false);
        $this->transpiler = new Transpiler(new Lexer(), new Parser(), $cache, new XSSFilter());
    }

    /**
     * Tests transpiling an else directive
     */
    public function testTranspilingElse()
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
    public function testTranspilingElseIf()
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
    public function testTranspilingEndFor()
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
    public function testTranspilingEndForeach()
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
    public function testTranspilingEndIf()
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
    public function testTranspilingEndPart()
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
    public function testTranspilingEndWhile()
    {
        $this->view->setContents('<% endwhile %>');
        $this->assertEquals(
            '<?php endwhile; ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    /**
     * Tests transpiling an extend directive
     */
    public function testTranspilingExtend()
    {
        $this->view->setContents('<% extends("foo.php") %>bar');
        $expected = [
            '<?php $__opulenceViewParent = $__opulenceViewFactory->create("foo.php");$__opulenceFortuneTranspiler->addParent($__opulenceViewParent, $__opulenceView);extract($__opulenceView->getVars()); ?>',
            '<?php $__opulenceParentContents = isset($__opulenceParentContents) ? $__opulenceParentContents : [];$__opulenceParentContents[] = $__opulenceFortuneTranspiler->transpile($__opulenceViewParent, $__opulenceViewParent->getContents()); ?>',
            'bar',
            '<?php echo eval("?>" . array_shift($__opulenceParentContents)); ?>'
        ];
        $this->assertEquals(
            implode(PHP_EOL, $expected),
            $this->transpiler->transpile($this->view)
        );
    }

    /**
     * Tests transpiling a for directive
     */
    public function testTranspilingFor()
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
    public function testTranspilingForElse()
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
    public function testTranspilingForIf()
    {
        $this->view->setContents('<% forif($foo as $bar) %>');
        $this->assertEquals(
            '<?php if(!isset($__opulenceForElseEmpty)): $__opulenceForElseEmpty = []; endif;$__opulenceForElseEmpty[] = true;' .
            'foreach($foo as $bar):' .
            '$__opulenceForElseEmpty[count($__opulenceForElseEmpty) - 1] = false; ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    /**
     * Tests transpiling a foreach directive
     */
    public function testTranspilingForeach()
    {
        $this->view->setContents('<% foreach($foo as $bar) %>');
        $this->assertEquals(
            '<?php foreach($foo as $bar): ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    /**
     * Tests transpiling an if directive
     */
    public function testTranspilingIf()
    {
        $this->view->setContents('<% if(true) %>');
        $this->assertEquals(
            '<?php if(true): ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    /**
     * Tests transpiling an include directive
     */
    public function testTranspilingInclude()
    {
        $this->view->setContents('<% include("foo.php") %>bar');
        $code = '<?php $__opulenceIncludedView = $__opulenceViewFactory->create("foo.php");';
        $code .= 'eval("?>" . $__opulenceFortuneTranspiler->transpile($__opulenceIncludedView, $__opulenceIncludedView->getContents())); ?>';
        $this->assertEquals(
            "{$code}bar",
            $this->transpiler->transpile($this->view)
        );
    }

    /**
     * Tests transpiling an include directive with passed variables
     */
    public function testTranspilingIncludeWithPassedVariables()
    {
        $this->view->setContents('<% include("foo.php", ["foo" => "bar"]) %>baz');
        $code = '<?php $__opulenceIncludedView = $__opulenceViewFactory->create("foo.php");';
        $code .= '$__opulenceIncludedView->setVars(["foo" => "bar"]);';
        $code .= 'eval("?>" . $__opulenceFortuneTranspiler->transpile($__opulenceIncludedView, $__opulenceIncludedView->getContents())); ?>';
        $this->assertEquals(
            "{$code}baz",
            $this->transpiler->transpile($this->view)
        );
    }

    /**
     * Tests transpiling a parent directive
     */
    public function testTranspilingParent()
    {
        $this->view->setContents('<% parent %>');
        $this->assertEquals(
            '__opulenceParentPlaceholder',
            $this->transpiler->transpile($this->view)
        );
    }

    /**
     * Tests transpiling a part directive
     */
    public function testTranspilingPart()
    {
        $this->view->setContents('<% part("foo") %>');
        $this->assertEquals(
            '<?php $__opulenceFortuneTranspiler->startPart("foo"); ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    /**
     * Tests transpiling a show directive
     */
    public function testTranspilingShow()
    {
        $this->view->setContents('<% show("foo") %>');
        $this->assertEquals(
            '<?php echo $__opulenceFortuneTranspiler->showPart("foo"); ?>',
            $this->transpiler->transpile($this->view)
        );
    }

    /**
     * Tests transpiling a while directive
     */
    public function testTranspilingWhile()
    {
        $this->view->setContents('<% while(true) %>');
        $this->assertEquals(
            '<?php while(true): ?>',
            $this->transpiler->transpile($this->view)
        );
    }
}
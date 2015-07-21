<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Test the Fortune directive compiler registrant
 */
namespace Opulence\Views\Compilers\Fortune;
use Opulence\Views\Filters\XSSFilter;
use Opulence\Views\Compilers\Fortune\Lexers\Lexer;
use Opulence\Views\Compilers\Fortune\Parsers\Parser;
use Opulence\Views\FortuneView;

class DirectiveCompilerRegistrantTest extends \PHPUnit_Framework_TestCase
{
    /** @var DirectiveCompilerRegistrant The registrant to use in tests */
    private $registrant = null;
    /** @var FortuneCompiler The compiler to use in tests */
    private $compiler = null;
    /** @var FortuneView The view to use in tests */
    private $view = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->view = new FortuneView();
        $this->registrant = new DirectiveCompilerRegistrant();
        $this->compiler = new FortuneCompiler(new Lexer(), new Parser(), new XSSFilter());
    }

    /**
     * Tests compiling an else directive
     */
    public function testCompilingElse()
    {
        $this->view->setContents('<% else %>');
        $this->assertEquals(
            '<?php else: ?>',
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling an else-if directive
     */
    public function testCompilingElseIf()
    {
        $this->view->setContents('<% elseif(true) %>');
        $this->assertEquals(
            '<?php elseif(true): ?>',
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling an else-if-empty directive
     */
    public function testCompilingElseIfEmpty()
    {
        $this->view->setContents('<% elseifempty %>');
        $this->assertEquals(
            '<?php endforeach; if(array_pop($__opulenceForElseEmpty)): ?>',
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling an end-for directive
     */
    public function testCompilingEndFor()
    {
        $this->view->setContents('<% endfor %>');
        $this->assertEquals(
            '<?php endfor; ?>',
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling an end-foreach directive
     */
    public function testCompilingEndForeach()
    {
        $this->view->setContents('<% endforeach %>');
        $this->assertEquals(
            '<?php endforeach; ?>',
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling an end-if directive
     */
    public function testCompilingEndIf()
    {
        $this->view->setContents('<% endif %>');
        $this->assertEquals(
            '<?php endif; ?>',
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling an end-part directive
     */
    public function testCompilingEndPart()
    {
        $this->view->setContents('<% endpart %>');
        $this->assertEquals(
            '<?php $__opulenceFortuneCompiler->endPart(); ?>',
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling an end-while directive
     */
    public function testCompilingEndWhile()
    {
        $this->view->setContents('<% endwhile %>');
        $this->assertEquals(
            '<?php endwhile; ?>',
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling an extend directive
     */
    public function testCompilingExtend()
    {
        $this->view->setContents('<% extends("foo.php") %>bar');
        $code = '<?php $__opulenceParentView = $__opulenceViewFactory->create("foo.php");';
        $code .= '$__opulenceCompiler->compile($__opulenceParentView, $__opulenceParentView->getContents()); ?>';
        $code = addcslashes($code, '"');
        $this->assertEquals(
            "<?php \$__opulenceFortuneCompiler->append(\"$code\"); ?>bar",
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling a for directive
     */
    public function testCompilingFor()
    {
        $this->view->setContents('<% for($i=0;$i<10;$i++) %>');
        $this->assertEquals(
            '<?php for($i=0;$i<10;$i++): ?>',
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling a for-else directive
     */
    public function testCompilingForElse()
    {
        $this->view->setContents('<% forelse($foo as $bar) %>');
        $this->assertEquals(
            '<?php if(!isset($__opulenceForElseEmpty): $__opulenceForElseEmpty = []; endif;$__opulenceForElseEmpty[] = true;' .
            'foreach($foo as $bar):' .
            '$__opulenceForElseEmpty[count($__opulenceForElseEmpty) - 1] = true; ?>',
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling a foreach directive
     */
    public function testCompilingForeach()
    {
        $this->view->setContents('<% foreach($foo as $bar) %>');
        $this->assertEquals(
            '<?php foreach($foo as $bar): ?>',
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling an if directive
     */
    public function testCompilingIf()
    {
        $this->view->setContents('<% if(true) %>');
        $this->assertEquals(
            '<?php if(true): ?>',
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling an include directive
     */
    public function testCompilingInclude()
    {
        $this->view->setContents('<% include("foo.php") %>bar');
        $code = '<?php $__opulenceIncludedView = $__opulenceViewFactory->create("foo.php");';
        $code .= '$__opulenceCompiler->compile($__opulenceIncludedView, $__opulenceIncludedView->getContents()); ?>';
        $this->assertEquals(
            "{$code}bar",
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling a parent directive
     */
    public function testCompilingParent()
    {
        $this->view->setContents('<% parent %>');
        $this->assertEquals(
            '__opulenceParentPlaceholder',
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling a part directive
     */
    public function testCompilingPart()
    {
        $this->view->setContents('<% part("foo") %>');
        $this->assertEquals(
            '<?php $__opulenceFortuneCompiler->startPart("foo"); ?>',
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling a show directive
     */
    public function testCompilingShow()
    {
        $this->view->setContents('<% show("foo") %>');
        $this->assertEquals(
            '<?php $__opulenceFortuneCompiler->showPart("foo"); ?>',
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }

    /**
     * Tests compiling a while directive
     */
    public function testCompilingWhile()
    {
        $this->view->setContents('<% while(true) %>');
        $this->assertEquals(
            '<?php while(true): ?>',
            $this->compiler->compile($this->view, $this->view->getContents())
        );
    }
}
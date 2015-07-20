<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Test the Fortune directive compiler registrant
 */
namespace Opulence\Views\Compilers\SubCompilers\Fortune;
use Opulence\Views\Compilers\Lexers\Lexer;
use Opulence\Views\Compilers\Parsers\Parser;
use Opulence\Views\Filters\XSSFilter;
use Opulence\Views\Template;

class FortuneDirectiveCompilerRegistrantTest extends \PHPUnit_Framework_TestCase
{
    /** @var FortuneDirectiveCompilerRegistrant The registrant to use in tests */
    private $registrant = null;
    /** @var FortuneCompiler The compiler to use in tests */
    private $compiler = null;
    /** @var Template The template to use in tests */
    private $template = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->template = new Template();
        $this->registrant = new FortuneDirectiveCompilerRegistrant();
        $this->compiler = new FortuneCompiler(new Lexer(), new Parser(), new XSSFilter());
    }

    /**
     * Tests compiling an else directive
     */
    public function testCompilingElse()
    {
        $this->template->setContents('<% else %>');
        $this->assertEquals(
            '<?php else: ?>',
            $this->compiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling an else-if directive
     */
    public function testCompilingElseIf()
    {
        $this->template->setContents('<% elseif(true) %>');
        $this->assertEquals(
            '<?php elseif(true): ?>',
            $this->compiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling an else-if-empty directive
     */
    public function testCompilingElseIfEmpty()
    {
        $this->template->setContents('<% elseifempty %>');
        $this->assertEquals(
            '<?php endforeach; if(array_pop($__opulenceForElseEmpty)): ?>',
            $this->compiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling an end-for directive
     */
    public function testCompilingEndFor()
    {
        $this->template->setContents('<% endfor %>');
        $this->assertEquals(
            '<?php endfor; ?>',
            $this->compiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling an end-foreach directive
     */
    public function testCompilingEndForeach()
    {
        $this->template->setContents('<% endforeach %>');
        $this->assertEquals(
            '<?php endforeach; ?>',
            $this->compiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling an end-if directive
     */
    public function testCompilingEndIf()
    {
        $this->template->setContents('<% endif %>');
        $this->assertEquals(
            '<?php endif; ?>',
            $this->compiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling an end-part directive
     */
    public function testCompilingEndPart()
    {
        $this->template->setContents('<% endpart %>');
        $this->assertEquals(
            '<?php $__opulenceFortuneCompiler->endPart(); ?>',
            $this->compiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling an end-while directive
     */
    public function testCompilingEndWhile()
    {
        $this->template->setContents('<% endwhile %>');
        $this->assertEquals(
            '<?php endwhile; ?>',
            $this->compiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling an extend directive
     */
    public function testCompilingExtend()
    {
        $this->template->setContents('<% extends("foo.php") %>bar');
        $code = '<?php $__opulenceParentTemplate = $__opulenceTemplateFactory->create("foo.php");';
        $code .= '$__opulenceCompiler->compile($__opulenceParentTemplate, $__opulenceParentTemplate->getContents()); ?>';
        $code = addcslashes($code, '"');
        $this->assertEquals(
            "<?php \$__opulenceFortuneCompiler->append(\"$code\"); ?>bar",
            $this->compiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling a for directive
     */
    public function testCompilingFor()
    {
        $this->template->setContents('<% for($i=0;$i<10;$i++) %>');
        $this->assertEquals(
            '<?php for($i=0;$i<10;$i++): ?>',
            $this->compiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling a for-else directive
     */
    public function testCompilingForElse()
    {
        $this->template->setContents('<% forelse($foo as $bar) %>');
        $this->assertEquals(
            '<?php if(!isset($__opulenceForElseEmpty): $__opulenceForElseEmpty = []; endif;$__opulenceForElseEmpty[] = true;' .
            'foreach($foo as $bar):' .
            '$__opulenceForElseEmpty[count($__opulenceForElseEmpty) - 1] = true; ?>',
            $this->compiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling a foreach directive
     */
    public function testCompilingForeach()
    {
        $this->template->setContents('<% foreach($foo as $bar) %>');
        $this->assertEquals(
            '<?php foreach($foo as $bar): ?>',
            $this->compiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling an if directive
     */
    public function testCompilingIf()
    {
        $this->template->setContents('<% if(true) %>');
        $this->assertEquals(
            '<?php if(true): ?>',
            $this->compiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling an include directive
     */
    public function testCompilingInclude()
    {
        $this->template->setContents('<% include("foo.php") %>bar');
        $code = '<?php $__opulenceIncludedTemplate = $__opulenceTemplateFactory->create("foo.php");';
        $code .= '$__opulenceCompiler->compile($__opulenceIncludedTemplate, $__opulenceIncludedTemplate->getContents()); ?>';
        $this->assertEquals(
            "{$code}bar",
            $this->compiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling a parent directive
     */
    public function testCompilingParent()
    {
        $this->template->setContents('<% parent %>');
        $this->assertEquals(
            '__opulenceParentPlaceholder',
            $this->compiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling a part directive
     */
    public function testCompilingPart()
    {
        $this->template->setContents('<% part("foo") %>');
        $this->assertEquals(
            '<?php $__opulenceFortuneCompiler->startPart("foo"); ?>',
            $this->compiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling a show directive
     */
    public function testCompilingShow()
    {
        $this->template->setContents('<% show("foo") %>');
        $this->assertEquals(
            '<?php $__opulenceFortuneCompiler->showPart("foo"); ?>',
            $this->compiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling a while directive
     */
    public function testCompilingWhile()
    {
        $this->template->setContents('<% while(true) %>');
        $this->assertEquals(
            '<?php while(true): ?>',
            $this->compiler->compile($this->template, $this->template->getContents())
        );
    }
}
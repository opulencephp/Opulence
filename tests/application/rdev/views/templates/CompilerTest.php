<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the template compiler
 */
namespace RDev\Views\Templates;

class CompilerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Compiler $compiler The compiler to use in tests */
    private $compiler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->compiler = new Compiler();
    }

    /**
     * Tests the compiler priorities
     */
    public function testCompilerPriority()
    {
        // Although this one is registered first, it doesn't have priority
        $this->compiler->registerCompiler(function ($content)
        {
            return $content . "2";
        }, false);
        // This one has priority, so it should be compiled first
        $this->compiler->registerCompiler(function ($content)
        {
            return $content . "1";
        }, true);
        $this->assertEquals("12", $this->compiler->compile(""));
    }

    /**
     * Tests registering an invalid compiler
     */
    public function testRegisteringInvalidCompiler()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->compiler->registerCompiler([]);
    }
} 
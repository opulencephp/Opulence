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
            return $content . "3";
        });
        // This one has the second highest priority, so it should be compiled second
        $this->compiler->registerCompiler(function ($content)
        {
            return $content . "2";
        }, 2);
        // This one has the highest priority, so it should be compiled first
        $this->compiler->registerCompiler(function ($content)
        {
            return $content . "1";
        }, 1);
        $this->assertEquals("123", $this->compiler->compile(""));
    }

    /**
     * Tests passing in an integer less than 1 for the priority
     */
    public function testIntegerLessThanOnePriority()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->compiler->registerCompiler(function ($content)
        {
            return $content;
        }, 0);
    }

    /**
     * Tests passing in a non-integer for the priority
     */
    public function testNonIntegerPriority()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->compiler->registerCompiler(function ($content)
        {
            return $content;
        }, 1.5);
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
<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Validation\Tests\Rules\Errors\Compilers;

use Opulence\Validation\Rules\Errors\Compilers\Compiler;

/**
 * Tests the error template compiler
 */
class CompilerTest extends \PHPUnit\Framework\TestCase
{
    /** @var Compiler The compiler to use in tests */
    private $compiler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->compiler = new Compiler();
    }

    /**
     * Tests compiling a template with arg placeholders
     */
    public function testCompilingTemplateWithArgPlaceholders()
    {
        $this->assertSame(
            'foo dave baz young',
            $this->compiler->compile(
                'foo',
                'foo :bar baz :blah',
                ['bar' => 'dave', 'blah' => 'young']
            )
        );
    }

    /**
     * Tests compiling a template with arg placeholders not in same order as args
     */
    public function testCompilingTemplateWithArgPlaceholdersNotInSameOrderAsArgs()
    {
        $this->assertSame(
            'foo dave baz young',
            $this->compiler->compile(
                'foo',
                'foo :bar baz :blah',
                ['blah' => 'young', 'bar' => 'dave']
            )
        );
    }

    /**
     * Tests compiling a template with field and arg placeholders
     */
    public function testCompilingTemplateWithFieldAndArgPlaceholders()
    {
        $this->assertSame(
            'foo the-field dave baz young',
            $this->compiler->compile(
                'the-field',
                'foo :field :bar baz :blah',
                ['bar' => 'dave', 'blah' => 'young']
            )
        );
    }

    /**
     * Tests compiling a template with a field placeholder
     */
    public function testCompilingTemplateWithFieldPlaceholder()
    {
        $this->assertSame(
            'foo bar',
            $this->compiler->compile('foo', ':field bar')
        );
    }

    /**
     * Tests compiling a with leftover placeholders
     */
    public function testCompilingTemplateWithLeftoverPlaceholders()
    {
        $this->assertSame(
            'foo dave',
            $this->compiler->compile('foo',
                'foo :bar :baz',
                ['bar' => 'dave']
            )
        );
    }

    /**
     * Tests compiling a template with no placeholders
     */
    public function testCompilingTemplateWithNoPlaceholders()
    {
        $this->assertSame(
            'foo bar',
            $this->compiler->compile('foo', 'foo bar')
        );
    }
}

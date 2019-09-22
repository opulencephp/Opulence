<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Validation\Tests\Rules\Errors\Compilers;

use Opulence\Validation\Rules\Errors\Compilers\Compiler;

/**
 * Tests the error template compiler
 */
class CompilerTest extends \PHPUnit\Framework\TestCase
{
    private Compiler $compiler;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->compiler = new Compiler();
    }

    /**
     * Tests compiling a template with arg placeholders
     */
    public function testCompilingTemplateWithArgPlaceholders(): void
    {
        $this->assertEquals(
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
    public function testCompilingTemplateWithArgPlaceholdersNotInSameOrderAsArgs(): void
    {
        $this->assertEquals(
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
    public function testCompilingTemplateWithFieldAndArgPlaceholders(): void
    {
        $this->assertEquals(
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
    public function testCompilingTemplateWithFieldPlaceholder(): void
    {
        $this->assertEquals(
            'foo bar',
            $this->compiler->compile('foo', ':field bar')
        );
    }

    /**
     * Tests compiling a with leftover placeholders
     */
    public function testCompilingTemplateWithLeftoverPlaceholders(): void
    {
        $this->assertEquals(
            'foo dave',
            $this->compiler->compile(
                'foo',
                'foo :bar :baz',
                ['bar' => 'dave']
            )
        );
    }

    /**
     * Tests compiling a template with no placeholders
     */
    public function testCompilingTemplateWithNoPlaceholders(): void
    {
        $this->assertEquals(
            'foo bar',
            $this->compiler->compile('foo', 'foo bar')
        );
    }
}

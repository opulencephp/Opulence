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

    protected function setUp(): void
    {
        $this->compiler = new Compiler();
    }

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

    public function testCompilingTemplateWithFieldPlaceholder(): void
    {
        $this->assertEquals(
            'foo bar',
            $this->compiler->compile('foo', ':field bar')
        );
    }

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

    public function testCompilingTemplateWithNoPlaceholders(): void
    {
        $this->assertEquals(
            'foo bar',
            $this->compiler->compile('foo', 'foo bar')
        );
    }
}

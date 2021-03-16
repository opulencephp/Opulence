<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Validation\Tests\Rules;

use InvalidArgumentException;
use LogicException;
use Opulence\Validation\Rules\EqualsFieldRule;

/**
 * Tests the equals field rule
 */
class EqualsFieldRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that equal values pass
     */
    public function testEqualValuesPass()
    {
        $rule = new EqualsFieldRule();
        $rule->setArgs(['foo']);
        $this->assertTrue($rule->passes('bar', ['foo' => 'bar']));
    }

    /**
     * Tests getting error placeholders
     */
    public function testGettingErrorPlaceholders()
    {
        $rule = new EqualsFieldRule();
        $rule->setArgs(['foo']);
        $this->assertEquals(['other' => 'foo'], $rule->getErrorPlaceholders());
    }

    /**
     * Tests getting the slug
     */
    public function testGettingSlug()
    {
        $rule = new EqualsFieldRule();
        $this->assertEquals('equalsField', $rule->getSlug());
    }

    /**
     * Tests not setting the args before passes
     */
    public function testNotSettingArgBeforePasses()
    {
        $this->expectException(LogicException::class);
        $rule = new EqualsFieldRule();
        $rule->passes('foo');
    }

    /**
     * Tests that null values pass
     */
    public function testNullValuesPass()
    {
        $rule = new EqualsFieldRule();
        $rule->setArgs(['foo']);
        $this->assertTrue($rule->passes(null));
    }

    /**
     * Tests passing an empty arg array
     */
    public function testPassingEmptyArgArray()
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new EqualsFieldRule();
        $rule->setArgs([]);
    }

    /**
     * Tests passing an invalid arg
     */
    public function testPassingInvalidArg()
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new EqualsFieldRule();
        $rule->setArgs([
            function () {
            }
        ]);
    }

    /**
     * Tests that unequal values fail
     */
    public function testUnequalValuesFail()
    {
        $rule = new EqualsFieldRule();
        $rule->setArgs(['foo']);
        $this->assertFalse($rule->passes('bar', ['foo' => 'baz']));
    }

    /**
     * Tests that unset, non-null values fail
     */
    public function testUnsetNonNullValuesFail()
    {
        $rule = new EqualsFieldRule();
        $rule->setArgs(['foo']);
        $this->assertFalse($rule->passes('bar'));
    }
}

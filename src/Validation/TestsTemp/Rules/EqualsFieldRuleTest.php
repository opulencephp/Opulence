<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Validation\TestsTemp\Rules;

use InvalidArgumentException;
use LogicException;
use Opulence\Validation\Rules\EqualsFieldRule;

/**
 * Tests the equals field rule
 */
class EqualsFieldRuleTest extends \PHPUnit\Framework\TestCase
{
    public function testEqualValuesPass(): void
    {
        $rule = new EqualsFieldRule();
        $rule->setArgs(['foo']);
        $this->assertTrue($rule->passes('bar', ['foo' => 'bar']));
    }

    public function testGettingErrorPlaceholders(): void
    {
        $rule = new EqualsFieldRule();
        $rule->setArgs(['foo']);
        $this->assertEquals(['other' => 'foo'], $rule->getErrorPlaceholders());
    }

    public function testGettingSlug(): void
    {
        $rule = new EqualsFieldRule();
        $this->assertEquals('equalsField', $rule->getSlug());
    }

    public function testNotSettingArgBeforePasses(): void
    {
        $this->expectException(LogicException::class);
        $rule = new EqualsFieldRule();
        $rule->passes('foo');
    }

    public function testNullValuesPass(): void
    {
        $rule = new EqualsFieldRule();
        $rule->setArgs(['foo']);
        $this->assertTrue($rule->passes(null));
    }

    public function testPassingEmptyArgArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new EqualsFieldRule();
        $rule->setArgs([]);
    }

    public function testPassingInvalidArg(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new EqualsFieldRule();
        $rule->setArgs([
            function () {
            }
        ]);
    }

    public function testUnequalValuesFail(): void
    {
        $rule = new EqualsFieldRule();
        $rule->setArgs(['foo']);
        $this->assertFalse($rule->passes('bar', ['foo' => 'baz']));
    }

    /**
     * Tests that unset, non-null values fail
     */
    public function testUnsetNonNullValuesFail(): void
    {
        $rule = new EqualsFieldRule();
        $rule->setArgs(['foo']);
        $this->assertFalse($rule->passes('bar'));
    }
}

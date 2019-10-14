<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Validation\Tests\Rules;

use InvalidArgumentException;
use LogicException;
use Opulence\Validation\Rules\MaxRule;

/**
 * Tests the max rule
 */
class MaxRuleTest extends \PHPUnit\Framework\TestCase
{
    public function testFailingRule(): void
    {
        $rule = new MaxRule();
        $rule->setArgs([1.5]);
        $this->assertFalse($rule->passes(2));
        $this->assertFalse($rule->passes(1.6));
    }

    public function testGettingErrorPlaceholders(): void
    {
        $rule = new MaxRule();
        $rule->setArgs([2]);
        $this->assertEquals(['max' => 2], $rule->getErrorPlaceholders());
    }

    public function testGettingSlug(): void
    {
        $rule = new MaxRule();
        $this->assertEquals('max', $rule->getSlug());
    }

    public function testNotSettingArgBeforePasses(): void
    {
        $this->expectException(LogicException::class);
        $rule = new MaxRule();
        $rule->passes(2);
    }

    public function testPassingEmptyArgArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new MaxRule();
        $rule->setArgs([]);
    }

    public function testPassingInvalidArg(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new MaxRule();
        $rule->setArgs([
            function () {
            }
        ]);
    }

    public function testPassingValue(): void
    {
        $rule = new MaxRule();
        $rule->setArgs([2]);
        $this->assertTrue($rule->passes(2));
        $this->assertTrue($rule->passes(1));
        $this->assertTrue($rule->passes(1.5));
    }

    public function testValueThatIsNotInclusive(): void
    {
        $rule = new MaxRule();
        $rule->setArgs([2, false]);
        $this->assertFalse($rule->passes(2));
        $this->assertTrue($rule->passes(1.9));
    }
}

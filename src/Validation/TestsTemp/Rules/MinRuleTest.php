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
use Opulence\Validation\Rules\MinRule;

/**
 * Tests the min rule
 */
class MinRuleTest extends \PHPUnit\Framework\TestCase
{
    public function testFailingRule(): void
    {
        $rule = new MinRule();
        $rule->setArgs([1.5]);
        $this->assertFalse($rule->passes(1));
        $this->assertFalse($rule->passes(1.4));
    }

    public function testGettingErrorPlaceholders(): void
    {
        $rule = new MinRule();
        $rule->setArgs([2]);
        $this->assertEquals(['min' => 2], $rule->getErrorPlaceholders());
    }

    public function testGettingSlug(): void
    {
        $rule = new MinRule();
        $this->assertEquals('min', $rule->getSlug());
    }

    public function testNotSettingArgBeforePasses(): void
    {
        $this->expectException(LogicException::class);
        $rule = new MinRule();
        $rule->passes(2);
    }

    public function testPassingEmptyArgArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new MinRule();
        $rule->setArgs([]);
    }

    public function testPassingInvalidArg(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new MinRule();
        $rule->setArgs([
            function () {
            }
        ]);
    }

    public function testPassingValue(): void
    {
        $rule = new MinRule();
        $rule->setArgs([1]);
        $this->assertTrue($rule->passes(1));
        $this->assertTrue($rule->passes(1.5));
        $this->assertTrue($rule->passes(2));
    }

    public function testValueThatIsNotInclusive(): void
    {
        $rule = new MinRule();
        $rule->setArgs([1, false]);
        $this->assertFalse($rule->passes(1));
        $this->assertTrue($rule->passes(1.1));
    }
}

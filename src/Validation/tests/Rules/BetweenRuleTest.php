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
use Opulence\Validation\Rules\BetweenRule;
use PHPUnit\Framework\TestCase;

/**
 * Tests the between rule
 */
class BetweenRuleTest extends TestCase
{
    public function testFailingRule(): void
    {
        $rule = new BetweenRule();
        $rule->setArgs([1, 2]);
        $this->assertFalse($rule->passes(.9));
        $this->assertFalse($rule->passes(2.1));
    }

    public function testGettingErrorPlaceholders(): void
    {
        $rule = new BetweenRule();
        $rule->setArgs([1, 2]);
        $this->assertEquals(['min' => 1, 'max' => 2], $rule->getErrorPlaceholders());
    }

    public function testGettingSlug(): void
    {
        $rule = new BetweenRule();
        $this->assertEquals('between', $rule->getSlug());
    }

    public function testNotSettingArgBeforePasses(): void
    {
        $this->expectException(LogicException::class);
        $rule = new BetweenRule();
        $rule->passes(2);
    }

    public function testPassingEmptyArgArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new BetweenRule();
        $rule->setArgs([]);
    }

    public function testPassingInvalidArg(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new BetweenRule();
        $rule->setArgs([
            function () {
            },
            function () {
            }
        ]);
    }

    public function testPassingValue(): void
    {
        $rule = new BetweenRule();
        $rule->setArgs([1, 2]);
        $this->assertTrue($rule->passes(1));
        $this->assertTrue($rule->passes(1.5));
        $this->assertTrue($rule->passes(2));
    }

    public function testValueThatIsNotInclusive(): void
    {
        $rule = new BetweenRule();
        $rule->setArgs([1, 2, false]);
        $this->assertFalse($rule->passes(1));
        $this->assertFalse($rule->passes(2));
        $this->assertTrue($rule->passes(1.5));
    }
}

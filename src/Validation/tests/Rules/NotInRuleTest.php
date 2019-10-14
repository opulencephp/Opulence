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
use Opulence\Validation\Rules\NotInRule;

/**
 * Tests the not-in-array rule
 */
class NotInRuleTest extends \PHPUnit\Framework\TestCase
{
    public function testGettingSlug(): void
    {
        $rule = new NotInRule();
        $this->assertEquals('notIn', $rule->getSlug());
    }

    public function testMatchingValuesPass(): void
    {
        $rule = new NotInRule();
        $rule->setArgs([['foo', 'bar']]);
        $this->assertTrue($rule->passes('baz'));
    }

    /**
     * Tests that non-matching values fail
     */
    public function testNonMatchingValuesFail(): void
    {
        $rule = new NotInRule();
        $rule->setArgs([['foo', 'bar']]);
        $this->assertFalse($rule->passes('foo'));
    }

    public function testNotSettingArgBeforePasses(): void
    {
        $this->expectException(LogicException::class);
        $rule = new NotInRule();
        $rule->passes('foo');
    }

    public function testPassingEmptyArgArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new NotInRule();
        $rule->setArgs([]);
    }

    public function testPassingInvalidArgs(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new NotInRule();
        $rule->setArgs([1]);
    }
}

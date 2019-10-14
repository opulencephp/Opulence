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

use Opulence\Validation\Rules\IntegerRule;

/**
 * Tests the integer rule
 */
class IntegerRuleTest extends \PHPUnit\Framework\TestCase
{
    public function testFailingValue(): void
    {
        $rule = new IntegerRule();
        $this->assertFalse($rule->passes(false));
        $this->assertFalse($rule->passes('foo'));
        $this->assertFalse($rule->passes(1.5));
        $this->assertFalse($rule->passes('1.5'));
    }

    public function testGettingSlug(): void
    {
        $rule = new IntegerRule();
        $this->assertEquals('integer', $rule->getSlug());
    }

    public function testPassingValue(): void
    {
        $rule = new IntegerRule();
        $this->assertTrue($rule->passes(0));
        $this->assertTrue($rule->passes(1));
    }
}

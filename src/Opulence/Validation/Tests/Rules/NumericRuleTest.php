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

use Opulence\Validation\Rules\NumericRule;

/**
 * Tests the numeric rule
 */
class NumericRuleTest extends \PHPUnit\Framework\TestCase
{
    public function testFailingValue(): void
    {
        $rule = new NumericRule();
        $this->assertFalse($rule->passes(false));
        $this->assertFalse($rule->passes('foo'));
    }

    public function testGettingSlug(): void
    {
        $rule = new NumericRule();
        $this->assertEquals('numeric', $rule->getSlug());
    }

    public function testPassingValue(): void
    {
        $rule = new NumericRule();
        $this->assertTrue($rule->passes(0));
        $this->assertTrue($rule->passes(1));
        $this->assertTrue($rule->passes(1.0));
        $this->assertTrue($rule->passes('1.0'));
    }
}

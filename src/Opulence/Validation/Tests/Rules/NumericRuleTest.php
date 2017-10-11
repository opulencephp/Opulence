<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Validation\Tests\Rules;

use Opulence\Validation\Rules\NumericRule;

/**
 * Tests the numeric rule
 */
class NumericRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that a failing value
     */
    public function testFailingValue()
    {
        $rule = new NumericRule();
        $this->assertFalse($rule->passes(false));
        $this->assertFalse($rule->passes('foo'));
    }

    /**
     * Tests getting the slug
     */
    public function testGettingSlug()
    {
        $rule = new NumericRule();
        $this->assertEquals('numeric', $rule->getSlug());
    }

    /**
     * Tests a passing value
     */
    public function testPassingValue()
    {
        $rule = new NumericRule();
        $this->assertTrue($rule->passes(0));
        $this->assertTrue($rule->passes(1));
        $this->assertTrue($rule->passes(1.0));
        $this->assertTrue($rule->passes('1.0'));
    }
}

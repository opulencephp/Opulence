<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Validation\Tests\Rules;

use Opulence\Validation\Rules\AlphaNumericRule;

/**
 * Tests the alpha-numeric rule
 */
class AlphaNumericRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that a failing value
     */
    public function testFailingValue()
    {
        $rule = new AlphaNumericRule();
        $this->assertFalse($rule->passes(''));
        $this->assertFalse($rule->passes('.'));
        $this->assertFalse($rule->passes('a1 b'));
    }

    /**
     * Tests getting the slug
     */
    public function testGettingSlug()
    {
        $rule = new AlphaNumericRule();
        $this->assertEquals('alphaNumeric', $rule->getSlug());
    }

    /**
     * Tests a passing value
     */
    public function testPassingValue()
    {
        $rule = new AlphaNumericRule();
        $this->assertTrue($rule->passes('1'));
        $this->assertTrue($rule->passes('a'));
        $this->assertTrue($rule->passes('a1'));
        $this->assertTrue($rule->passes('1abc'));
    }
}

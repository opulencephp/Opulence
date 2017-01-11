<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules;

/**
 * Tests the alphabetic rule
 */
class AlphaRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that a failing value
     */
    public function testFailingValue()
    {
        $rule = new AlphaRule();
        $this->assertFalse($rule->passes(''));
        $this->assertFalse($rule->passes('1'));
        $this->assertFalse($rule->passes('a b'));
    }

    /**
     * Tests getting the slug
     */
    public function testGettingSlug()
    {
        $rule = new AlphaRule();
        $this->assertEquals('alpha', $rule->getSlug());
    }

    /**
     * Tests a passing value
     */
    public function testPassingValue()
    {
        $rule = new AlphaRule();
        $this->assertTrue($rule->passes('a'));
        $this->assertTrue($rule->passes('abc'));
    }
}

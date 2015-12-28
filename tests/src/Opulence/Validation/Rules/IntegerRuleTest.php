<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules;

/**
 * Tests the integer rule
 */
class IntegerRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that a failing value
     */
    public function testFailingValue()
    {
        $rule = new IntegerRule();
        $this->assertFalse($rule->passes(false));
        $this->assertFalse($rule->passes("foo"));
        $this->assertFalse($rule->passes(1.5));
        $this->assertFalse($rule->passes("1.5"));
    }

    /**
     * Tests getting the slug
     */
    public function testGettingSlug()
    {
        $rule = new IntegerRule();
        $this->assertEquals("integer", $rule->getSlug());
    }

    /**
     * Tests a passing value
     */
    public function testPassingValue()
    {
        $rule = new IntegerRule();
        $this->assertTrue($rule->passes(0));
        $this->assertTrue($rule->passes(1));
    }
}
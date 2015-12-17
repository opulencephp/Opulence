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
 * Tests the equals rule
 */
class EqualsRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that equal values pass
     */
    public function testEqualValuesPass()
    {
        $rule = new EqualsRule("foo");
        $this->assertTrue($rule->passes("foo"));
    }

    /**
     * Tests that unequal values fail
     */
    public function testUnequalValuesFail()
    {
        $rule = new EqualsRule("foo");
        $this->assertFalse($rule->passes("bar"));
    }
}
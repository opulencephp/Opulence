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
 * Tests the equals field rule
 */
class EqualsFieldRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that equal values pass
     */
    public function testEqualValuesPass()
    {
        $rule = new EqualsFieldRule("foo");
        $this->assertTrue($rule->passes("bar", ["foo" => "bar"]));
    }

    /**
     * Tests that null values pass
     */
    public function testNullValuesPass()
    {
        $rule = new EqualsFieldRule("foo");
        $this->assertTrue($rule->passes(null));
    }

    /**
     * Tests that unequal values fail
     */
    public function testUnequalValuesFail()
    {
        $rule = new EqualsFieldRule("foo");
        $this->assertFalse($rule->passes("bar", ["foo" => "baz"]));
    }

    /**
     * Tests that unset, non-null values fail
     */
    public function testUnsetNonNullValuesFail()
    {
        $rule = new EqualsFieldRule("foo");
        $this->assertFalse($rule->passes("bar"));
    }
}
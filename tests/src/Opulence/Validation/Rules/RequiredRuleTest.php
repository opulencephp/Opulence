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
 * Tests the required rule
 */
class RequiredRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that a set value passes
     */
    public function testSetValuePasses()
    {
        $rule = new RequiredRule();
        $this->assertTrue($rule->passes(0));
        $this->assertTrue($rule->passes(true));
        $this->assertTrue($rule->passes(false));
        $this->assertTrue($rule->passes("foo"));
    }

    /**
     * Tests that an unset value fails
     */
    public function testUnsetValueFails()
    {
        $rule = new RequiredRule();
        $this->assertFalse($rule->passes(null));
        $this->assertFalse($rule->passes(""));
    }
}
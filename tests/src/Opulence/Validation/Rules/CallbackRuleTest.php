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
 * Tests the callback rule
 */
class CallbackRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that the callback is executed
     */
    public function testCallbackIsExecuted()
    {
        $correctInputWasPassed = false;
        $callback = function ($value, array $inputs = []) use (&$correctInputWasPassed) {
            $correctInputWasPassed = $value == "foo" && $inputs == ["bar" => "baz"];

            return true;
        };
        $rule = new CallbackRule($callback);
        $rule->passes("foo", ["bar" => "baz"]);
        $this->assertTrue($correctInputWasPassed);
    }

    /**
     * Tests that the callback's return value is respected
     */
    public function testCallbackReturnValueIsRespected()
    {
        $trueCallback = function ($value, array $inputs = []) {
            return true;
        };
        $falseCallback = function ($value, array $inputs = []) {
            return false;
        };
        $passRule = new CallbackRule($trueCallback);
        $failRule = new CallbackRule($falseCallback);
        $this->assertTrue($passRule->passes("foo"));
        $this->assertFalse($failRule->passes("bar"));
    }
}
<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Validation\Tests\Rules;

use InvalidArgumentException;
use LogicException;
use Opulence\Validation\Rules\CallbackRule;

/**
 * Tests the callback rule
 */
class CallbackRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that the callback is executed
     */
    public function testCallbackIsExecuted()
    {
        $correctInputWasPassed = false;
        $callback = function ($value, array $inputs = []) use (&$correctInputWasPassed) {
            $correctInputWasPassed = $value === 'foo' && $inputs === ['bar' => 'baz'];

            return true;
        };
        $rule = new CallbackRule();
        $rule->setArgs([$callback]);
        $rule->passes('foo', ['bar' => 'baz']);
        $this->assertTrue($correctInputWasPassed);
    }

    /**
     * Tests that the callback's return value is respected
     */
    public function testCallbackReturnValueIsRespected()
    {
        $trueCallback = function () {
            return true;
        };
        $falseCallback = function () {
            return false;
        };
        $passRule = new CallbackRule();
        $failRule = new CallbackRule();
        $passRule->setArgs([$trueCallback]);
        $failRule->setArgs([$falseCallback]);
        $this->assertTrue($passRule->passes('foo'));
        $this->assertFalse($failRule->passes('bar'));
    }

    /**
     * Tests getting the slug
     */
    public function testGettingSlug()
    {
        $rule = new CallbackRule();
        $this->assertEquals('callback', $rule->getSlug());
    }

    /**
     * Tests not setting the args before passes
     */
    public function testNotSettingArgBeforePasses()
    {
        $this->expectException(LogicException::class);
        $rule = new CallbackRule();
        $rule->passes('foo');
    }

    /**
     * Tests passing an empty arg array
     */
    public function testPassingEmptyArgArray()
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new CallbackRule();
        $rule->setArgs([]);
    }

    /**
     * Tests passing an invalid arg
     */
    public function testPassingInvalidArg()
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new CallbackRule();
        $rule->setArgs(['foo']);
    }
}

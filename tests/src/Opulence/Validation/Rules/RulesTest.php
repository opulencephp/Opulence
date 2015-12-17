<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules;

use BadMethodCallException;
use LogicException;

/**
 * Tests the rules
 */
class RulesTest extends \PHPUnit_Framework_TestCase
{
    /** @var Rules The rules to use in the tests */
    private $rules = null;
    /** @var RuleExtensionRegistry|\PHPUnit_Framework_MockObject_MockObject The registry to use in tests */
    private $registry = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->registry = $this->getMock(RuleExtensionRegistry::class);
        $this->rules = new Rules($this->registry);
    }

    /*
     * Tests the email rule
     */

    /**
     * Tests calling an extension
     */
    public function testCallingExtension()
    {
        $this->registry->expects($this->once())
            ->method("has")
            ->with("foo")
            ->willReturn($this->getMock(IRule::class));
        $rule = $this->getMock(IRule::class);
        $this->registry->expects($this->once())
            ->method("get")
            ->willReturn($rule);
        $rule->expects($this->once())
            ->method("passes")
            ->with("bar")
            ->willReturn(true);
        $this->assertSame($this->rules, $this->rules->foo("bar"));
        $this->assertTrue($this->rules->passes("name", "bar"));
    }

    /*
     * Tests the equals rule
     */

    /**
     * Tests calling non-existent extension
     */
    public function testCallingNonExistentExtension()
    {
        $this->setExpectedException(BadMethodCallException::class);
        $this->registry->expects($this->once())
            ->method("has")
            ->with("foo")
            ->willReturn(false);
        $this->rules->foo("bar");
    }

    public function testEmailRule()
    {
        $this->assertSame($this->rules, $this->rules->email());
        $this->assertTrue($this->rules->passes("foo", "foo@bar.com"));
    }

    public function testEqualsFieldRule()
    {
        $this->assertSame($this->rules, $this->rules->equalsField("bar"));
        $this->assertTrue($this->rules->passes("foo", "baz", ["bar" => "baz"]));
    }

    public function testEqualsRule()
    {
        $this->assertSame($this->rules, $this->rules->equals("bar"));
        $this->assertTrue($this->rules->passes("foo", "bar"));
    }

    /**
     * Tests that an exception is thrown when nesting conditions
     */
    public function testExceptionThrownWhenNestingConditions()
    {
        $this->setExpectedException(LogicException::class);
        $this->rules->condition(function () {
        });
        $this->rules->condition(function () {
        });
    }

    /*
     * Tests the equals field rule
     */

    public function testRequiredRule()
    {
        $this->assertSame($this->rules, $this->rules->required());
        $this->assertTrue($this->rules->passes("foo", "bar"));
    }

    /**
     * Tests that rules added after conditions are always respected
     */
    public function testRulesAddedAfterConditionAreAlwaysRespected()
    {
        $this->rules->required()
            ->condition(function () {
                return false;
            })
            ->email()
            ->endCondition()
            ->equals("bar");
        $this->assertTrue($this->rules->passes("foo", "bar"));
    }

    /*
     * Tests the required rule
     */

    /**
     * Tests that rules added before conditions are always respected
     */
    public function testRulesAddedBeforeConditionAreAlwaysRespected()
    {
        $this->rules->required()
            ->condition(function () {
                return false;
            })
            ->email();
        $this->assertTrue($this->rules->passes("foo", "bar"));
    }

    /**
     * Tests that rules in condition are respected
     */
    public function testRulesInConditionAreRespected()
    {
        $this->rules->condition(function () {
            return true;
        })
            ->email();
        $this->assertTrue($this->rules->passes("foo", "foo@bar.com"));
        $this->assertFalse($this->rules->passes("foo", "bar"));
    }

    /**
     * Tests that it passes with no rules
     */
    public function testsPassesWithNoRules()
    {
        $this->assertTrue($this->rules->passes("foo", "bar"));
    }
}
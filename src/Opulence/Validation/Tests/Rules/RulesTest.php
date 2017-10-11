<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Validation\Tests\Rules;

use BadMethodCallException;
use Countable;
use DateTime;
use LogicException;
use Opulence\Validation\Rules\Errors\Compilers\ICompiler;
use Opulence\Validation\Rules\Errors\ErrorTemplateRegistry;
use Opulence\Validation\Rules\IRule;
use Opulence\Validation\Rules\IRuleWithArgs;
use Opulence\Validation\Rules\RuleExtensionRegistry;
use Opulence\Validation\Rules\Rules;

/**
 * Tests the rules
 */
class RulesTest extends \PHPUnit\Framework\TestCase
{
    /** @var Rules The rules to use in the tests */
    private $rules = null;
    /** @var RuleExtensionRegistry|\PHPUnit_Framework_MockObject_MockObject The rule extension registry */
    private $ruleExtensionRegistry = null;
    /** @var ErrorTemplateRegistry|\PHPUnit_Framework_MockObject_MockObject The error template registry */
    private $errorTemplateRegistry = null;
    /** @var ICompiler|\PHPUnit_Framework_MockObject_MockObject The error template compiler */
    private $errorTemplateCompiler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->ruleExtensionRegistry = $this->createMock(RuleExtensionRegistry::class);
        $this->errorTemplateRegistry = $this->createMock(ErrorTemplateRegistry::class);
        $this->errorTemplateCompiler = $this->createMock(ICompiler::class);
        $this->rules = new Rules(
            $this->ruleExtensionRegistry,
            $this->errorTemplateRegistry,
            $this->errorTemplateCompiler
        );
    }

    /**
     * Tests the alpha-numeric rule
     */
    public function testAlphaNumericRule()
    {
        $this->assertSame($this->rules, $this->rules->alphaNumeric());
        $this->assertTrue($this->rules->pass('a1'));
        $this->assertFalse($this->rules->pass('a 1'));
    }

    /**
     * Tests the alpha rule
     */
    public function testAlphaRule()
    {
        $this->assertSame($this->rules, $this->rules->alpha());
        $this->assertTrue($this->rules->pass('a'));
        $this->assertFalse($this->rules->pass('a1'));
    }

    /**
     * Tests the between rule
     */
    public function testBetweenRule()
    {
        $this->assertSame($this->rules, $this->rules->between(1, 2, false));
        $this->assertFalse($this->rules->pass(1));
        $this->assertFalse($this->rules->pass(2));
        $this->assertTrue($this->rules->pass(1.5));
    }

    /**
     * Tests calling an extension
     */
    public function testCallingExtension()
    {
        $this->ruleExtensionRegistry->expects($this->once())
            ->method('hasRule')
            ->with('foo')
            ->willReturn(true);
        $rule = $this->createMock(IRule::class);
        $this->ruleExtensionRegistry->expects($this->once())
            ->method('getRule')
            ->willReturn($rule);
        $rule->expects($this->once())
            ->method('passes')
            ->with('bar')
            ->willReturn(true);
        $this->assertSame($this->rules, $this->rules->foo());
        $this->assertTrue($this->rules->pass('bar'));
    }

    /**
     * Tests calling an extension with args
     */
    public function testCallingExtensionWithArgs()
    {
        $this->ruleExtensionRegistry->expects($this->once())
            ->method('hasRule')
            ->with('foo')
            ->willReturn(true);
        $rule = $this->createMock(IRuleWithArgs::class);
        $rule->expects($this->once())
            ->method('setArgs')
            ->with(['baz']);
        $this->ruleExtensionRegistry->expects($this->once())
            ->method('getRule')
            ->willReturn($rule);
        $rule->expects($this->once())
            ->method('passes')
            ->with('bar')
            ->willReturn(true);
        $this->assertSame($this->rules, $this->rules->foo('baz'));
        $this->assertTrue($this->rules->pass('bar'));
    }

    /**
     * Tests calling non-existent extension
     */
    public function testCallingNonExistentExtension()
    {
        $this->expectException(BadMethodCallException::class);
        $this->ruleExtensionRegistry->expects($this->once())
            ->method('hasRule')
            ->with('foo')
            ->willReturn(false);
        $this->rules->foo('bar');
    }

    /**
     * Tests that checking rules twice does not append errors
     */
    public function testCheckingRulesTwiceDoesNotAppendErrors()
    {
        $this->errorTemplateRegistry->expects($this->exactly(2))
            ->method('getErrorTemplate')
            ->with('the-field', 'email')
            ->willReturn('');
        $this->errorTemplateCompiler->expects($this->exactly(2))
            ->method('compile')
            ->with('the-field', '', [])
            ->willReturn('The error');
        $this->rules->email();
        $this->rules->pass('foo');
        $this->assertEquals(['The error'], $this->rules->getErrors('the-field'));
        $this->rules->pass('foo');
        $this->assertEquals(['The error'], $this->rules->getErrors('the-field'));
    }

    /**
     * Test that a conditional rule's rules' errors are added
     */
    public function testConditionalRulesErrorsAreAdded()
    {
        $this->errorTemplateRegistry->expects($this->at(0))
            ->method('getErrorTemplate')
            ->with('the-field', 'equals')
            ->willReturn('equals template');
        $this->errorTemplateRegistry->expects($this->at(1))
            ->method('getErrorTemplate')
            ->with('the-field', 'email')
            ->willReturn('email template');
        $this->errorTemplateCompiler->expects($this->at(0))
            ->method('compile')
            ->with('the-field', 'equals template', [])
            ->willReturn('equals error');
        $this->errorTemplateCompiler->expects($this->at(1))
            ->method('compile')
            ->with('the-field', 'email template', [])
            ->willReturn('email error');
        $this->rules->condition(function () {
            return true;
        });
        $this->rules->equals('foo');
        $this->rules->email();
        $this->rules->pass('bar');
        $this->assertEquals(
            ['equals error', 'email error'],
            $this->rules->getErrors('the-field')
        );
    }

    /*
     * Tests the date rule
     */
    public function testDateRule()
    {
        $format1 = 'Y-m-d';
        $format2 = 'F j';
        $this->assertSame($this->rules, $this->rules->date([$format1, $format2]));
        $this->assertTrue($this->rules->pass((new DateTime)->format($format1)));
        $this->assertTrue($this->rules->pass((new DateTime)->format($format2)));
    }

    /**
     * Tests the email rule
     */
    public function testEmailRule()
    {
        $this->assertSame($this->rules, $this->rules->email());
        $this->assertTrue($this->rules->pass('foo@bar.com'));
    }

    /**
     * Tests the equals field rule
     */
    public function testEqualsFieldRule()
    {
        $this->assertSame($this->rules, $this->rules->equalsField('bar'));
        $this->assertTrue($this->rules->pass('baz', ['bar' => 'baz']));
    }

    /*
     * Tests the equals rule
     */
    public function testEqualsRule()
    {
        $this->assertSame($this->rules, $this->rules->equals('bar'));
        $this->assertTrue($this->rules->pass('bar'));
    }

    /**
     * Tests that an exception is thrown when nesting conditions
     */
    public function testExceptionThrownWhenNestingConditions()
    {
        $this->expectException(LogicException::class);
        $this->rules->condition(function () {
        });
        $this->rules->condition(function () {
        });
    }

    /**
     * Tests getting the errors when there are none
     */
    public function testGettingErrorsWhenThereAreNone()
    {
        $this->assertEquals([], $this->rules->getErrors('foo'));
        $this->rules->email();
        $this->rules->pass('foo@bar.com');
        $this->assertEquals([], $this->rules->getErrors('foo'));
    }

    /**
     * Tests halting the field validation does nothing on passing rules
     */
    public function testHaltingFieldValidationDoesNothingOnPassingRules()
    {
        /** @var IRule|\PHPUnit_Framework_MockObject_MockObject $rule1 */
        $rule1 = $this->createMock(IRule::class);
        /** @var IRule|\PHPUnit_Framework_MockObject_MockObject $rule2 */
        $rule2 = $this->createMock(IRule::class);
        $rule1->expects($this->once())
            ->method('passes')
            ->willReturn(true);
        $rule2->expects($this->once())
            ->method('passes')
            ->willReturn(true);
        $this->ruleExtensionRegistry->expects($this->at(0))
            ->method('hasRule')
            ->with('foo')
            ->willReturn(true);
        $this->ruleExtensionRegistry->expects($this->at(1))
            ->method('getRule')
            ->with('foo')
            ->willReturn($rule1);
        $this->ruleExtensionRegistry->expects($this->at(2))
            ->method('hasRule')
            ->with('bar')
            ->willReturn(true);
        $this->ruleExtensionRegistry->expects($this->at(3))
            ->method('getRule')
            ->with('bar')
            ->willReturn($rule2);
        $this->rules->foo();
        $this->rules->bar();
        $this->assertTrue($this->rules->pass('blah', [], true));
    }

    /**
     * Tests halting the field validation on failure
     */
    public function testHaltingFieldValidationOnFailure()
    {
        /** @var IRule|\PHPUnit_Framework_MockObject_MockObject $rule1 */
        $rule1 = $this->createMock(IRule::class);
        /** @var IRule|\PHPUnit_Framework_MockObject_MockObject $rule2 */
        $rule2 = $this->createMock(IRule::class);
        $rule1->expects($this->once())
            ->method('passes')
            ->willReturn(false);
        $rule2->expects($this->never())
            ->method('passes');
        $this->ruleExtensionRegistry->expects($this->at(0))
            ->method('hasRule')
            ->with('foo')
            ->willReturn(true);
        $this->ruleExtensionRegistry->expects($this->at(1))
            ->method('getRule')
            ->with('foo')
            ->willReturn($rule1);
        $this->ruleExtensionRegistry->expects($this->at(2))
            ->method('hasRule')
            ->with('bar')
            ->willReturn(true);
        $this->ruleExtensionRegistry->expects($this->at(3))
            ->method('getRule')
            ->with('bar')
            ->willReturn($rule2);
        $this->rules->foo();
        $this->rules->bar();
        $this->assertFalse($this->rules->pass('blah', [], true));
    }

    /**
     * Tests the IP address rule
     */
    public function testIPAddressRule()
    {
        $this->assertSame($this->rules, $this->rules->ipAddress());
        $this->assertTrue($this->rules->pass('127.0.0.1'));
    }

    /**
     * Tests the in rule
     */
    public function testInRule()
    {
        $this->assertSame($this->rules, $this->rules->in(['foo', 'bar']));
        $this->assertTrue($this->rules->pass('bar'));
    }

    /**
     * Tests the integer rule
     */
    public function testIntegerRule()
    {
        $this->assertSame($this->rules, $this->rules->integer());
        $this->assertTrue($this->rules->pass(1));
    }

    /**
     * Tests the maximum rule
     */
    public function testMaxRule()
    {
        $this->assertSame($this->rules, $this->rules->max(2, false));
        $this->assertFalse($this->rules->pass(2));
        $this->assertTrue($this->rules->pass(1.9));
    }

    /**
     * Tests the minimum rule
     */
    public function testMinRule()
    {
        $this->assertSame($this->rules, $this->rules->min(2, false));
        $this->assertFalse($this->rules->pass(2));
        $this->assertTrue($this->rules->pass(2.1));
    }

    /**
     * Tests that a non-required field passes all rules when empty
     */
    public function testNonRequiredFieldPassesAllRulesWhenEmpty()
    {
        $this->rules
            ->email()
            ->date('Y-m-d');
        $this->assertTrue($this->rules->pass(null));
        $this->assertTrue($this->rules->pass([]));
        $countable = $this->createMock(Countable::class);
        $countable->expects($this->exactly(2))
            ->method('count')
            ->willReturn(0);
        $this->assertTrue($this->rules->pass($countable));
    }

    /**
     * Tests the not-in rule
     */
    public function testNotInRule()
    {
        $this->assertSame($this->rules, $this->rules->notIn(['foo', 'bar']));
        $this->assertTrue($this->rules->pass('baz'));
    }

    /**
     * Tests the numeric rule
     */
    public function testNumericRule()
    {
        $this->assertSame($this->rules, $this->rules->numeric());
        $this->assertTrue($this->rules->pass(1.5));
    }

    /**
     * Tests the regex rule
     */
    public function testRegexRule()
    {
        $this->assertSame($this->rules, $this->rules->regex('/^[a-z]{3}$/'));
        $this->assertTrue($this->rules->pass('baz'));
    }

    /**
     * Tests the required rule
     */
    public function testRequiredRule()
    {
        $this->assertSame($this->rules, $this->rules->required());
        $this->assertTrue($this->rules->pass('bar'));
    }

    /**
     * Tests that rule extensions in a condition are respected
     */
    public function testRuleExtensionsInConditionAreRespected()
    {
        $this->ruleExtensionRegistry->expects($this->once())
            ->method('hasRule')
            ->with('foo')
            ->willReturn(true);
        $rule = $this->createMock(IRule::class);
        $rule->expects($this->never())
            ->method('passes');
        $this->ruleExtensionRegistry->expects($this->once())
            ->method('getRule')
            ->with('foo')
            ->willReturn($rule);
        $this->rules->condition(function () {
            return false;
        })
            ->foo();
        $this->assertTrue($this->rules->pass('bar'));
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
            ->equals('bar');
        $this->assertTrue($this->rules->pass('bar'));
    }

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
        $this->assertTrue($this->rules->pass('bar'));
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
        $this->assertTrue($this->rules->pass('foo@bar.com'));
        $this->assertFalse($this->rules->pass('bar'));
    }

    /**
     * Tests that it passes with no rules
     */
    public function testsPassesWithNoRules()
    {
        $this->assertTrue($this->rules->pass('bar'));
    }
}

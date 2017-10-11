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
use Opulence\Validation\Rules\CallbackRule;
use Opulence\Validation\Rules\RuleExtensionRegistry;

/**
 * Tests the rule extension registry
 */
class RuleExtensionRegistryTest extends \PHPUnit\Framework\TestCase
{
    /** @var RuleExtensionRegistry The registry to use in tests */
    private $registry = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->registry = new RuleExtensionRegistry();
    }

    /**
     * Tests that a callback is converted to a rule
     */
    public function testCallbackGetsConvertedToRule()
    {
        $rule = function () {
            return true;
        };
        /** @var IRule|\PHPUnit_Framework_MockObject_MockObject $rule */
        $this->registry->registerRuleExtension($rule, 'foo');
        $this->assertInstanceOf(CallbackRule::class, $this->registry->getRule('foo'));
        $this->assertTrue($this->registry->getRule('foo')->passes('bar'));
    }

    /**
     * Tests checking if the registry has a rule
     */
    public function testCheckingIfRegistryHasRule()
    {
        /** @var IRule|\PHPUnit_Framework_MockObject_MockObject $rule */
        $rule = $this->createMock(IRule::class);
        $rule->expects($this->once())
            ->method('getSlug')
            ->willReturn('foo');
        $this->registry->registerRuleExtension($rule);
        $this->assertTrue($this->registry->hasRule('foo'));
        $this->assertFalse($this->registry->hasRule('bar'));
    }

    /**
     * Tests an exception is thrown when no extension is found
     */
    public function testExceptionThrownWhenNoExtensionExists()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->registry->getRule('foo');
    }

    /**
     * Tests an exception is thrown when registering an invalid rule
     */
    public function testExceptionThrownWhenRegisteringAnInvalidRule()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->registry->registerRuleExtension('foo', 'bar');
    }

    /**
     * Tests getting a rule object
     */
    public function testGettingRuleObject()
    {
        /** @var IRule|\PHPUnit_Framework_MockObject_MockObject $rule */
        $rule = $this->createMock(IRule::class);
        $rule->expects($this->once())
            ->method('getSlug')
            ->willReturn('foo');
        $this->registry->registerRuleExtension($rule);
        $this->assertSame($rule, $this->registry->getRule('foo'));
    }

    /**
     * Tests that the slug is ignored if registering a rule object
     */
    public function testSlugIgnoredIfRegisteringRuleObject()
    {
        /** @var IRule|\PHPUnit_Framework_MockObject_MockObject $rule */
        $rule = $this->createMock(IRule::class);
        $rule->expects($this->once())
            ->method('getSlug')
            ->willReturn('foo');
        $this->registry->registerRuleExtension($rule, 'bar');
        $this->assertTrue($this->registry->hasRule('foo'));
    }
}

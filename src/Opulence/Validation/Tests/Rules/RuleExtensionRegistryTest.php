<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Validation\Tests\Rules;

use InvalidArgumentException;
use Opulence\Validation\Rules\CallbackRule;
use Opulence\Validation\Rules\IRule;
use Opulence\Validation\Rules\RuleExtensionRegistry;
use PHPUnit\Framework\MockObject\MockObject;

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
    protected function setUp() : void
    {
        $this->registry = new RuleExtensionRegistry();
    }

    /**
     * Tests that a callback is converted to a rule
     */
    public function testCallbackGetsConvertedToRule() : void
    {
        $rule = function () {
            return true;
        };
        /** @var IRule|MockObject $rule */
        $this->registry->registerRuleExtension($rule, 'foo');
        $this->assertInstanceOf(CallbackRule::class, $this->registry->getRule('foo'));
        $this->assertTrue($this->registry->getRule('foo')->passes('bar'));
    }

    /**
     * Tests checking if the registry has a rule
     */
    public function testCheckingIfRegistryHasRule() : void
    {
        /** @var IRule|MockObject $rule */
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
    public function testExceptionThrownWhenNoExtensionExists() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->registry->getRule('foo');
    }

    /**
     * Tests an exception is thrown when registering an invalid rule
     */
    public function testExceptionThrownWhenRegisteringAnInvalidRule() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->registry->registerRuleExtension('foo', 'bar');
    }

    /**
     * Tests getting a rule object
     */
    public function testGettingRuleObject() : void
    {
        /** @var IRule|MockObject $rule */
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
    public function testSlugIgnoredIfRegisteringRuleObject() : void
    {
        /** @var IRule|MockObject $rule */
        $rule = $this->createMock(IRule::class);
        $rule->expects($this->once())
            ->method('getSlug')
            ->willReturn('foo');
        $this->registry->registerRuleExtension($rule, 'bar');
        $this->assertTrue($this->registry->hasRule('foo'));
    }
}

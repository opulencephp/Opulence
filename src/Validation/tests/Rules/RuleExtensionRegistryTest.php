<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Validation\Tests\Rules;

use InvalidArgumentException;
use Opulence\Validation\Rules\CallbackRule;
use Opulence\Validation\Rules\IRule;
use Opulence\Validation\Rules\RuleExtensionRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests the rule extension registry
 */
class RuleExtensionRegistryTest extends TestCase
{
    /** @var RuleExtensionRegistry The registry to use in tests */
    private RuleExtensionRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = new RuleExtensionRegistry();
    }

    public function testCallbackGetsConvertedToRule(): void
    {
        $rule = function () {
            return true;
        };
        /** @var IRule|MockObject $rule */
        $this->registry->registerRuleExtension($rule, 'foo');
        $this->assertInstanceOf(CallbackRule::class, $this->registry->getRule('foo'));
        $this->assertTrue($this->registry->getRule('foo')->passes('bar'));
    }

    public function testCheckingIfRegistryHasRule(): void
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

    public function testExceptionThrownWhenNoExtensionExists(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->registry->getRule('foo');
    }

    public function testExceptionThrownWhenRegisteringAnInvalidRule(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->registry->registerRuleExtension('foo', 'bar');
    }

    public function testGettingRuleObject(): void
    {
        /** @var IRule|MockObject $rule */
        $rule = $this->createMock(IRule::class);
        $rule->expects($this->once())
            ->method('getSlug')
            ->willReturn('foo');
        $this->registry->registerRuleExtension($rule);
        $this->assertSame($rule, $this->registry->getRule('foo'));
    }

    public function testSlugIgnoredIfRegisteringRuleObject(): void
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

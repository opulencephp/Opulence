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
use LogicException;
use Opulence\Validation\Rules\BetweenRule;

/**
 * Tests the between rule
 */
class BetweenRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests a failing rule
     */
    public function testFailingRule() : void
    {
        $rule = new BetweenRule();
        $rule->setArgs([1, 2]);
        $this->assertFalse($rule->passes(.9));
        $this->assertFalse($rule->passes(2.1));
    }

    /**
     * Tests getting error placeholders
     */
    public function testGettingErrorPlaceholders() : void
    {
        $rule = new BetweenRule();
        $rule->setArgs([1, 2]);
        $this->assertEquals(['min' => 1, 'max' => 2], $rule->getErrorPlaceholders());
    }

    /**
     * Tests getting the slug
     */
    public function testGettingSlug() : void
    {
        $rule = new BetweenRule();
        $this->assertEquals('between', $rule->getSlug());
    }

    /**
     * Tests not setting the args before passes
     */
    public function testNotSettingArgBeforePasses() : void
    {
        $this->expectException(LogicException::class);
        $rule = new BetweenRule();
        $rule->passes(2);
    }

    /**
     * Tests passing an empty arg array
     */
    public function testPassingEmptyArgArray() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new BetweenRule();
        $rule->setArgs([]);
    }

    /**
     * Tests passing an invalid arg
     */
    public function testPassingInvalidArg() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new BetweenRule();
        $rule->setArgs([
            function () {
            },
            function () {
            }
        ]);
    }

    /**
     * Tests a passing value
     */
    public function testPassingValue() : void
    {
        $rule = new BetweenRule();
        $rule->setArgs([1, 2]);
        $this->assertTrue($rule->passes(1));
        $this->assertTrue($rule->passes(1.5));
        $this->assertTrue($rule->passes(2));
    }

    /**
     * Tests a value that is not inclusive
     */
    public function testValueThatIsNotInclusive() : void
    {
        $rule = new BetweenRule();
        $rule->setArgs([1, 2, false]);
        $this->assertFalse($rule->passes(1));
        $this->assertFalse($rule->passes(2));
        $this->assertTrue($rule->passes(1.5));
    }
}

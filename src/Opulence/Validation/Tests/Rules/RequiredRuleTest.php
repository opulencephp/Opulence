<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Validation\Tests\Rules;

use Countable;
use Opulence\Validation\Rules\RequiredRule;

/**
 * Tests the required rule
 */
class RequiredRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that an empty array fails
     */
    public function testEmptyArrayFails()
    {
        $rule = new RequiredRule();
        $this->assertFalse($rule->passes([]));
        $countable = $this->createMock(Countable::class);
        $countable->expects($this->once())
            ->method('count')
            ->willReturn(0);
        $this->assertFalse($rule->passes($countable));
    }

    /**
     * Tests getting the slug
     */
    public function testGettingSlug()
    {
        $rule = new RequiredRule();
        $this->assertEquals('required', $rule->getSlug());
    }

    /**
     * Tests that a set value passes
     */
    public function testSetValuePasses()
    {
        $rule = new RequiredRule();
        $this->assertTrue($rule->passes(0));
        $this->assertTrue($rule->passes(true));
        $this->assertTrue($rule->passes(false));
        $this->assertTrue($rule->passes('foo'));
    }

    /**
     * Tests that an unset value fails
     */
    public function testUnsetValueFails()
    {
        $rule = new RequiredRule();
        $this->assertFalse($rule->passes(null));
        $this->assertFalse($rule->passes(''));
    }
}

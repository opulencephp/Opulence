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
use LogicException;
use Opulence\Validation\Rules\InRule;

/**
 * Tests the in-array rule
 */
class InRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getting the slug
     */
    public function testGettingSlug(): void
    {
        $rule = new InRule();
        $this->assertEquals('in', $rule->getSlug());
    }

    /**
     * Tests that matching values pass
     */
    public function testMatchingValuesPass(): void
    {
        $rule = new InRule();
        $rule->setArgs([['foo', 'bar']]);
        $this->assertTrue($rule->passes('foo'));
    }

    /**
     * Tests that non-matching values fail
     */
    public function testNonMatchingValuesFail(): void
    {
        $rule = new InRule();
        $rule->setArgs([['foo', 'bar']]);
        $this->assertFalse($rule->passes('baz'));
    }

    /**
     * Tests not setting the args before passes
     */
    public function testNotSettingArgBeforePasses(): void
    {
        $this->expectException(LogicException::class);
        $rule = new InRule();
        $rule->passes('foo');
    }

    /**
     * Tests passing an empty arg array
     */
    public function testPassingEmptyArgArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new InRule();
        $rule->setArgs([]);
    }

    /**
     * Tests passing invalid args
     */
    public function testPassingInvalidArgs(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new InRule();
        $rule->setArgs([1]);
    }
}

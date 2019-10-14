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
use Opulence\Validation\Rules\EqualsRule;

/**
 * Tests the equals rule
 */
class EqualsRuleTest extends \PHPUnit\Framework\TestCase
{
    public function testEqualValuesPass(): void
    {
        $rule = new EqualsRule();
        $rule->setArgs(['foo']);
        $this->assertTrue($rule->passes('foo'));
    }

    public function testGettingSlug(): void
    {
        $rule = new EqualsRule();
        $this->assertEquals('equals', $rule->getSlug());
    }

    public function testPassingEmptyArgArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new EqualsRule();
        $rule->setArgs([]);
    }

    public function testUnequalValuesFail(): void
    {
        $rule = new EqualsRule();
        $rule->setArgs(['foo']);
        $this->assertFalse($rule->passes('bar'));
    }
}

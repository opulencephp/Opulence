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

use DateTime;
use InvalidArgumentException;
use Opulence\Validation\Rules\DateRule;

/**
 * Tests the date rule
 */
class DateRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that equal values pass
     */
    public function testEqualValuesPass(): void
    {
        $rule = new DateRule();
        $format1 = 'F j';
        $format2 = 's:i:H d-m-Y';
        $rule->setArgs([$format1]);
        $this->assertTrue($rule->passes((new DateTime)->format($format1)));
        $rule->setArgs([[$format1, $format2]]);
        $this->assertTrue($rule->passes((new DateTime)->format($format2)));
    }

    /**
     * Tests getting the slug
     */
    public function testGettingSlug(): void
    {
        $rule = new DateRule();
        $this->assertEquals('date', $rule->getSlug());
    }

    /**
     * Tests passing an invalid arg type
     */
    public function testInvalidArgType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new DateRule();
        $rule->setArgs([1]);
    }

    /**
     * Tests passing an empty arg array
     */
    public function testPassingEmptyArgArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new DateRule();
        $rule->setArgs([]);
    }

    /**
     * Tests that unequal values fail
     */
    public function testUnequalValuesFail(): void
    {
        $rule = new DateRule();
        $format1 = 'F j';
        $format2 = 's:i:H d-m-Y';
        $rule->setArgs([$format1]);
        $this->assertFalse($rule->passes((new DateTime)->format('His')));
        $rule->setArgs([[$format1, $format2]]);
        $this->assertFalse($rule->passes((new DateTime)->format('Y')));
    }
}

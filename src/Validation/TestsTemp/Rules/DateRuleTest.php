<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Validation\TestsTemp\Rules;

use DateTime;
use InvalidArgumentException;
use Opulence\Validation\Rules\DateRule;

/**
 * Tests the date rule
 */
class DateRuleTest extends \PHPUnit\Framework\TestCase
{
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

    public function testGettingSlug(): void
    {
        $rule = new DateRule();
        $this->assertEquals('date', $rule->getSlug());
    }

    public function testInvalidArgType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new DateRule();
        $rule->setArgs([1]);
    }

    public function testPassingEmptyArgArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new DateRule();
        $rule->setArgs([]);
    }

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

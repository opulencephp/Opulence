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

use Opulence\Validation\Rules\AlphaNumericRule;
use PHPUnit\Framework\TestCase;

/**
 * Tests the alpha-numeric rule
 */
class AlphaNumericRuleTest extends TestCase
{
    public function testFailingValue(): void
    {
        $rule = new AlphaNumericRule();
        $this->assertFalse($rule->passes(''));
        $this->assertFalse($rule->passes('.'));
        $this->assertFalse($rule->passes('a1 b'));
    }

    public function testGettingSlug(): void
    {
        $rule = new AlphaNumericRule();
        $this->assertEquals('alphaNumeric', $rule->getSlug());
    }

    public function testPassingValue(): void
    {
        $rule = new AlphaNumericRule();
        $this->assertTrue($rule->passes('1'));
        $this->assertTrue($rule->passes('a'));
        $this->assertTrue($rule->passes('a1'));
        $this->assertTrue($rule->passes('1abc'));
    }
}

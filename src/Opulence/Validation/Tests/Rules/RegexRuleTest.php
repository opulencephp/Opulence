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
use Opulence\Validation\Rules\RegexRule;

/**
 * Tests the regex rule
 */
class RegexRuleTest extends \PHPUnit\Framework\TestCase
{
    public function testGettingSlug(): void
    {
        $rule = new RegexRule();
        $this->assertEquals('regex', $rule->getSlug());
    }

    public function testMatchingValuesPass(): void
    {
        $rule = new RegexRule();
        $rule->setArgs(['/^[a-z]{3}$/']);
        $this->assertTrue($rule->passes('foo'));
    }

    /**
     * Tests that non-matching values fail
     */
    public function testNonMatchingValuesFail(): void
    {
        $rule = new RegexRule();
        $rule->setArgs(['/^[a-z]{3}$/']);
        $this->assertFalse($rule->passes('a'));
    }

    public function testNotSettingArgBeforePasses(): void
    {
        $this->expectException(LogicException::class);
        $rule = new RegexRule();
        $rule->passes('foo');
    }

    public function testPassingEmptyArgArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new RegexRule();
        $rule->setArgs([]);
    }

    public function testPassingInvalidArgs(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new RegexRule();
        $rule->setArgs([1]);
    }
}

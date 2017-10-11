<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Validation\Tests\Rules;

use InvalidArgumentException;
use LogicException;
use Opulence\Validation\Rules\RegexRule;

/**
 * Tests the regex rule
 */
class RegexRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getting the slug
     */
    public function testGettingSlug()
    {
        $rule = new RegexRule();
        $this->assertEquals('regex', $rule->getSlug());
    }

    /**
     * Tests that matching values pass
     */
    public function testMatchingValuesPass()
    {
        $rule = new RegexRule();
        $rule->setArgs(['/^[a-z]{3}$/']);
        $this->assertTrue($rule->passes('foo'));
    }

    /**
     * Tests that non-matching values fail
     */
    public function testNonMatchingValuesFail()
    {
        $rule = new RegexRule();
        $rule->setArgs(['/^[a-z]{3}$/']);
        $this->assertFalse($rule->passes('a'));
    }

    /**
     * Tests not setting the args before passes
     */
    public function testNotSettingArgBeforePasses()
    {
        $this->expectException(LogicException::class);
        $rule = new RegexRule();
        $rule->passes('foo');
    }

    /**
     * Tests passing an empty arg array
     */
    public function testPassingEmptyArgArray()
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new RegexRule();
        $rule->setArgs([]);
    }

    /**
     * Tests passing invalid args
     */
    public function testPassingInvalidArgs()
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new RegexRule();
        $rule->setArgs([1]);
    }
}

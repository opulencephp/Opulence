<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Validation\Tests\Rules;

use Opulence\Validation\Rules\IPAddressRule;

/**
 * Tests the IP address rule
 */
class IPAddressRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that a failing value
     */
    public function testFailingValue()
    {
        $rule = new IPAddressRule();
        $this->assertFalse($rule->passes(''));
        $this->assertFalse($rule->passes('123'));
    }

    /**
     * Tests getting the slug
     */
    public function testGettingSlug()
    {
        $rule = new IPAddressRule();
        $this->assertEquals('ipAddress', $rule->getSlug());
    }

    /**
     * Tests a passing value
     */
    public function testPassingValue()
    {
        $rule = new IPAddressRule();
        $this->assertTrue($rule->passes('127.0.0.1'));
    }
}

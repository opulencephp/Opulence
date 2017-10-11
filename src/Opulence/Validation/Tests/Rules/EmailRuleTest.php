<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Validation\Tests\Rules;

use Opulence\Validation\Rules\EmailRule;

/**
 * Tests the email rule
 */
class EmailRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getting the slug
     */
    public function testGettingSlug()
    {
        $rule = new EmailRule();
        $this->assertEquals('email', $rule->getSlug());
    }

    /**
     * Tests that an invalid email fails
     */
    public function testInvalidEmailFails()
    {
        $rule = new EmailRule();
        $this->assertFalse($rule->passes('foo'));
    }

    /**
     * Tests that a valid email passes
     */
    public function testValidEmailPasses()
    {
        $rule = new EmailRule();
        $this->assertTrue($rule->passes('foo@bar.com'));
    }
}

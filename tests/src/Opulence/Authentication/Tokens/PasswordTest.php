<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens;

use DateTimeImmutable;

/**
 * Tests the password
 */
class PasswordTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that Bcrypt is used by default
     */
    public function testBcryptHashing()
    {
        $validFromDate = new DateTimeImmutable("1970-01-01 01:00:00");
        $validToDate = new DateTimeImmutable("3000-01-01 01:00:00");
        $hashedValue = Password::hash("blah", "foo", ["cost" => 4]);
        $token = new Password(1, 2, $hashedValue, $validFromDate, $validToDate, true);
        $this->assertTrue($token->verify("foo"));
    }
}
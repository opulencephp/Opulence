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
use Opulence\Tests\Authentication\Tokens\Mocks\Token;

/**
 * Tests the token class
 */
class TokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests seeing if a token with a valid-from value in the future is expired
     */
    public function testCheckingIsActiveWithFutureValidFrom()
    {
        $validFrom = new DateTimeImmutable("+1 day");
        $validTo = new DateTimeImmutable("+1 week");
        $token = new Token(1, 2, "", $validFrom, $validTo, true);
        $this->assertFalse($token->isActive());
    }

    /**
     * Tests seeing if a token with a valid-to value in the future is expired
     */
    public function testCheckingIsActiveWithFutureValidTo()
    {
        $validFrom = new DateTimeImmutable("now");
        $validTo = new DateTimeImmutable("+1 week");
        $token = new Token(1, 2, "", $validFrom, $validTo, true);
        $this->assertTrue($token->isActive());
    }

    /**
     * Tests checking if a token is active when its active flag was set to false
     */
    public function testCheckingIsActiveWithInactiveToken()
    {
        $validFrom = new DateTimeImmutable("-1 week");
        $validTo = new DateTimeImmutable("+1 week");
        $token = new Token(1, 2, "", $validFrom, $validTo, false);
        $this->assertFalse($token->isActive());
    }

    /**
     * Tests seeing if a token with a valid-from value in the past is expired
     */
    public function testCheckingIsActiveWithPastValidFrom()
    {
        $validFrom = new DateTimeImmutable("-1 week");
        $validTo = new DateTimeImmutable("+1 week");
        $token = new Token(1, 2, "", $validFrom, $validTo, true);
        $this->assertTrue($token->isActive());
    }

    /**
     * Tests seeing if a token with a valid-to value in the past is expired
     */
    public function testCheckingIsActiveWithPastValidTo()
    {
        $validFrom = new DateTimeImmutable("now");
        $validTo = new DateTimeImmutable("-1 week");
        $token = new Token(1, 2, "", $validFrom, $validTo, true);
        $this->assertFalse($token->isActive());
    }

    /**
     * Tests getting the hashed value from the token
     */
    public function testGettingHashedValue()
    {
        $hashedValue = "foo";
        $validFrom = new DateTimeImmutable("-1 week");
        $validTo = new DateTimeImmutable("+1 week");
        $token = new Token(1, 2, $hashedValue, $validFrom, $validTo, false);
        $this->assertEquals($hashedValue, $token->getHashedValue());
    }

    /**
     * Tests getting the Id
     */
    public function testGettingId()
    {
        $id = 1;
        $validFrom = new DateTimeImmutable("-1 week");
        $validTo = new DateTimeImmutable("+1 week");
        $token = new Token($id, 2, "", $validFrom, $validTo, true);
        $this->assertEquals($id, $token->getId());
    }

    /**
     * Tests getting the user Id
     */
    public function testGettingUserId()
    {
        $validFrom = new DateTimeImmutable("-1 week");
        $validTo = new DateTimeImmutable("+1 week");
        $token = new Token(1, 2, "", $validFrom, $validTo, true);
        $this->assertEquals(2, $token->getUserId());
    }

    /**
     * Tests getting the valid-from date
     */
    public function testGettingValidFromDate()
    {
        $validFromDate = new DateTimeImmutable("1776-07-04 12:34:56");
        $token = new Token(1, 2, "", $validFromDate, new DateTimeImmutable("1970-01-01 01:00:00"), true);
        $this->assertEquals($validFromDate, $token->getValidFrom());
    }

    /**
     * Tests getting the valid-to date
     */
    public function testGettingValidToDate()
    {
        $validToDate = new DateTimeImmutable("1970-01-01 01:00:00");
        $token = new Token(1, 2, "", new DateTimeImmutable("1776-07-04 12:34:56"), $validToDate, true);
        $this->assertEquals($validToDate, $token->getValidTo());
    }

    /**
     * Tests setting the Id
     */
    public function testSettingId()
    {
        $oldId = 1;
        $newId = 2;
        $validFrom = new DateTimeImmutable("-1 week");
        $validTo = new DateTimeImmutable("+1 week");
        $token = new Token($oldId, 2, "", $validFrom, $validTo, true);
        $token->setId($newId);
        $this->assertEquals($newId, $token->getId());
    }
} 
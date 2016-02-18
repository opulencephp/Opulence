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
     * Tests Bcrypt hashing
     */
    public function testBcryptHashing()
    {
        $validFromDate = new DateTimeImmutable("1970-01-01 01:00:00");
        $validToDate = new DateTimeImmutable("3000-01-01 01:00:00");
        $hashedValue = Token::hash(Algorithms::BCRYPT, "foo", ["cost" => 4]);
        $token = new Token(1, 2, Algorithms::BCRYPT, $hashedValue, $validFromDate, $validToDate, true);
        $this->assertTrue($token->verify("foo"));
    }

    /**
     * Tests seeing if a token with a valid-from value in the future is expired
     */
    public function testCheckingIsActiveWithFutureValidFrom()
    {
        $validFrom = new DateTimeImmutable("+1 day");
        $validTo = new DateTimeImmutable("+1 week");
        $token = new Token(1, 2, Algorithms::SHA256, "", $validFrom, $validTo, true);
        $this->assertFalse($token->isActive());
    }

    /**
     * Tests seeing if a token with a valid-to value in the future is expired
     */
    public function testCheckingIsActiveWithFutureValidTo()
    {
        $validFrom = new DateTimeImmutable("now");
        $validTo = new DateTimeImmutable("+1 week");
        $token = new Token(1, 2, Algorithms::SHA256, "", $validFrom, $validTo, true);
        $this->assertTrue($token->isActive());
    }

    /**
     * Tests checking if a token is active when its active flag was set to false
     */
    public function testCheckingIsActiveWithInactiveToken()
    {
        $validFrom = new DateTimeImmutable("-1 week");
        $validTo = new DateTimeImmutable("+1 week");
        $token = new Token(1, 2, Algorithms::SHA256, "", $validFrom, $validTo, false);
        $this->assertFalse($token->isActive());
    }

    /**
     * Tests seeing if a token with a valid-from value in the past is expired
     */
    public function testCheckingIsActiveWithPastValidFrom()
    {
        $validFrom = new DateTimeImmutable("-1 week");
        $validTo = new DateTimeImmutable("+1 week");
        $token = new Token(1, 2, Algorithms::SHA256, "", $validFrom, $validTo, true);
        $this->assertTrue($token->isActive());
    }

    /**
     * Tests seeing if a token with a valid-to value in the past is expired
     */
    public function testCheckingIsActiveWithPastValidTo()
    {
        $validFrom = new DateTimeImmutable("now");
        $validTo = new DateTimeImmutable("-1 week");
        $token = new Token(1, 2, Algorithms::SHA256, "", $validFrom, $validTo, true);
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
        $token = new Token(1, 2, Algorithms::SHA256, $hashedValue, $validFrom, $validTo, false);
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
        $token = new Token($id, 2, Algorithms::SHA256, "", $validFrom, $validTo, true);
        $this->assertEquals($id, $token->getId());
    }

    /**
     * Tests getting the user Id
     */
    public function testGettingUserId()
    {
        $validFrom = new DateTimeImmutable("-1 week");
        $validTo = new DateTimeImmutable("+1 week");
        $token = new Token(1, 2, Algorithms::SHA256, "", $validFrom, $validTo, true);
        $this->assertEquals(2, $token->getUserId());
    }

    /**
     * Tests getting the valid-from date
     */
    public function testGettingValidFromDate()
    {
        $validFromDate = new DateTimeImmutable("1776-07-04 12:34:56");
        $token = new Token(1, 2, Algorithms::SHA256, "", $validFromDate, new DateTimeImmutable("1970-01-01"), true);
        $this->assertEquals($validFromDate, $token->getValidFrom());
    }

    /**
     * Tests getting the valid-to date
     */
    public function testGettingValidToDate()
    {
        $validToDate = new DateTimeImmutable("1970-01-01 01:00:00");
        $token = new Token(1, 2, Algorithms::SHA256, "", new DateTimeImmutable("1776-07-04"), $validToDate, true);
        $this->assertEquals($validToDate, $token->getValidTo());
    }

    /**
     * Tests hashing values using all algorithms
     */
    public function testHashingValueUsingAllAlgorithms()
    {
        $algorithms = [
            Algorithms::CRC32 => crc32("foo"),
            Algorithms::MD5 => md5("foo"),
            Algorithms::SHA1 => hash("sha1", "foo"),
            Algorithms::SHA256 => hash("sha256", "foo"),
            Algorithms::SHA512 => hash("sha512", "foo")
        ];

        foreach ($algorithms as $algorithm => $expectedValue) {
            $this->assertEquals($expectedValue, Token::hash($algorithm, "foo"));
        }
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
        $token = new Token($oldId, 2, Algorithms::SHA256, "", $validFrom, $validTo, true);
        $token->setId($newId);
        $this->assertEquals($newId, $token->getId());
    }

    /**
     * Tests verifying a hash
     */
    public function testVerifyingHash()
    {
        $algorithms = [
            Algorithms::BCRYPT => password_hash("foo", PASSWORD_BCRYPT),
            Algorithms::CRC32 => crc32("foo"),
            Algorithms::MD5 => md5("foo"),
            Algorithms::SHA1 => hash("sha1", "foo"),
            Algorithms::SHA256 => hash("sha256", "foo"),
            Algorithms::SHA512 => hash("sha512", "foo")
        ];
        $validFromDate = new DateTimeImmutable("1970-01-01 01:00:00");
        $validToDate = new DateTimeImmutable("3000-01-01 01:00:00");

        foreach ($algorithms as $algorithm => $hashedValue) {
            $token = new Token(1, 2, $algorithm, $hashedValue, $validFromDate, $validToDate, true);
            $this->assertTrue($token->verify("foo"));
            $this->assertFalse($token->verify("bar"));
        }
    }
} 
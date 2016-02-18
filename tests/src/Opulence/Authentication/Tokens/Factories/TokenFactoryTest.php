<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens\Factories;

use DateTimeImmutable;
use Opulence\Authentication\Tokens\Algorithms;
use Opulence\Authentication\Tokens\Token;

/**
 * Tests the token factory
 */
class TokenFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var TokenFactory The factory to use in tests */
    private $factory = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->factory = new TokenFactory();
    }

    /**
     * Tests that the hashed token is set
     */
    public function testHashedTokenIsSet()
    {
        $unhashedValue = "";
        $token = $this->factory->createToken(1, Algorithms::SHA256, new DateTimeImmutable(), new DateTimeImmutable(),
            $unhashedValue);
        $this->assertEquals(Token::hash(Algorithms::SHA256, $unhashedValue), $token->getHashedValue());
        $this->assertNotEquals($unhashedValue, $token->getHashedValue());
    }

    /**
     * Tests that an instance of token is created
     */
    public function testInstanceOfTokenCreated()
    {
        $unhashedValue = "";
        $this->assertInstanceOf(
            Token::class,
            $this->factory->createToken(1, Algorithms::SHA256, new DateTimeImmutable(), new DateTimeImmutable(),
                $unhashedValue)
        );
    }

    /**
     * Tests that the properties are correctly set on the token
     */
    public function testPropertiesCorrectlySetOnToken()
    {
        $unhashedValue = "";
        $validFrom = new DateTimeImmutable();
        $validTo = new DateTimeImmutable("+1 week");
        $token = $this->factory->createToken(1, Algorithms::SHA256, $validFrom, $validTo, $unhashedValue);
        $this->assertEquals(-1, $token->getId());
        $this->assertEquals(Token::hash(Algorithms::SHA256, $unhashedValue), $token->getHashedValue());
        $this->assertEquals(1, $token->getUserId());
        $this->assertSame($validFrom, $token->getValidFrom());
        $this->assertSame($validTo, $token->getValidTo());
        $this->assertTrue($token->isActive());
    }
}
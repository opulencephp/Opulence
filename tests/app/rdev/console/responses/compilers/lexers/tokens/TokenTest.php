<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the response token
 */
namespace RDev\Console\Responses\Compilers\Lexers\Tokens;

class TokenTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Token The token to use in tests */
    private $token = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->token = new Token(TokenTypes::T_WORD, "foo", 24);
    }

    /**
     * Tests getting the position
     */
    public function testGettingPosition()
    {
        $this->assertEquals(24, $this->token->getPosition());
    }

    /**
     * Tests getting the type
     */
    public function testGettingType()
    {
        $this->assertEquals(TokenTypes::T_WORD, $this->token->getType());
    }

    /**
     * Tests getting the value
     */
    public function testGettingValue()
    {
        $this->assertEquals("foo", $this->token->getValue());
    }
}
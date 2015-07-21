<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the view token
 */
namespace Opulence\Views\Compilers\Fortune\Lexers\Tokens;

class TokenTest extends \PHPUnit_Framework_TestCase
{
    /** @var Token The token to use in tests */
    private $token = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->token = new Token(TokenTypes::T_EXPRESSION, "foo", 1);
    }

    /**
     * Tests getting the line
     */
    public function testGettingLine()
    {
        $this->assertEquals(1, $this->token->getLine());
    }

    /**
     * Tests getting the type
     */
    public function testGettingType()
    {
        $this->assertEquals(TokenTypes::T_EXPRESSION, $this->token->getType());
    }

    /**
     * Tests getting the value
     */
    public function testGettingValue()
    {
        $this->assertEquals("foo", $this->token->getValue());
    }
}
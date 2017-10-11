<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Views\Tests\Compilers\Fortune\Lexers\Tokens;

use Opulence\Views\Compilers\Fortune\Lexers\Tokens\Token;
use Opulence\Views\Compilers\Fortune\Lexers\Tokens\TokenTypes;

/**
 * Tests the view token
 */
class TokenTest extends \PHPUnit\Framework\TestCase
{
    /** @var Token The token to use in tests */
    private $token = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->token = new Token(TokenTypes::T_EXPRESSION, 'foo', 1);
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
        $this->assertEquals('foo', $this->token->getValue());
    }
}

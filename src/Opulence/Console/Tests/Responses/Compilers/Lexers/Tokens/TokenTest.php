<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Console\Tests\Responses\Compilers\Lexers\Tokens;

use Opulence\Console\Responses\Compilers\Lexers\Tokens\Token;
use Opulence\Console\Responses\Compilers\Lexers\Tokens\TokenTypes;

/**
 * Tests the response token
 */
class TokenTest extends \PHPUnit\Framework\TestCase
{
    /** @var Token The token to use in tests */
    private $token = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->token = new Token(TokenTypes::T_WORD, 'foo', 24);
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
        $this->assertEquals('foo', $this->token->getValue());
    }
}

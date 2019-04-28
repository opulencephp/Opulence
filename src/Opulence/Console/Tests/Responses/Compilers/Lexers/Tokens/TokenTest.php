<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Tests\Responses\Compilers\Lexers\Tokens;

use Opulence\Console\Responses\Compilers\Lexers\Tokens\Token;
use Opulence\Console\Responses\Compilers\Lexers\Tokens\TokenTypes;

/**
 * Tests the response token
 */
class TokenTest extends \PHPUnit\Framework\TestCase
{
    /** @var Token The token to use in tests */
    private $token;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->token = new Token(TokenTypes::T_WORD, 'foo', 24);
    }

    /**
     * Tests getting the position
     */
    public function testGettingPosition(): void
    {
        $this->assertEquals(24, $this->token->getPosition());
    }

    /**
     * Tests getting the type
     */
    public function testGettingType(): void
    {
        $this->assertEquals(TokenTypes::T_WORD, $this->token->getType());
    }

    /**
     * Tests getting the value
     */
    public function testGettingValue(): void
    {
        $this->assertEquals('foo', $this->token->getValue());
    }
}

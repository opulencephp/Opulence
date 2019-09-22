<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\Tests\Compilers\Fortune\Lexers\Tokens;

use Opulence\Views\Compilers\Fortune\Lexers\Tokens\Token;
use Opulence\Views\Compilers\Fortune\Lexers\Tokens\TokenTypes;

/**
 * Tests the view token
 */
class TokenTest extends \PHPUnit\Framework\TestCase
{
    private Token $token;

    protected function setUp(): void
    {
        $this->token = new Token(TokenTypes::T_EXPRESSION, 'foo', 1);
    }

    public function testGettingLine(): void
    {
        $this->assertEquals(1, $this->token->getLine());
    }

    public function testGettingType(): void
    {
        $this->assertEquals(TokenTypes::T_EXPRESSION, $this->token->getType());
    }

    public function testGettingValue(): void
    {
        $this->assertEquals('foo', $this->token->getValue());
    }
}

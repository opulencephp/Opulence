<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Authentication\Tokens\Mocks;

use DateTimeImmutable;
use Opulence\Authentication\Tokens\Token as BaseToken;

/**
 * Mocks the token for use in testing
 */
class Token extends BaseToken
{
    /**
     * Creates a new token for use in testing
     *
     * @return Token An instantiated token class
     */
    public static function create()
    {
        return new Token(1, 2, "foo", new DateTimeImmutable("now"), new DateTimeImmutable("+1 week"), true);
    }
} 
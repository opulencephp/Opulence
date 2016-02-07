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
use Opulence\Authentication\Tokens\IToken;
use Opulence\Authentication\Tokens\Token;

/**
 * Defines the token factory
 */
class TokenFactory implements ITokenFactory
{
    /** The default token length */
    const DEFAULT_TOKEN_LENGTH = 32;
    /** @var int The length of generated tokens */
    protected $tokenLength = self::DEFAULT_TOKEN_LENGTH;

    /**
     * @param int $tokenLength The length of the token
     */
    public function __construct(int $tokenLength = self::DEFAULT_TOKEN_LENGTH)
    {
        $this->tokenLength = $tokenLength;
    }

    /**
     * @inheritdoc
     */
    public function createToken(
        $userId,
        DateTimeImmutable $validFrom,
        DateTimeImmutable $validTo,
        string &$unhashedToken
    ) : IToken
    {
        $unhashedToken = $this->generateRandomString($this->tokenLength);

        return new Token(-1, $userId, Token::hash($unhashedToken), $validFrom, $validTo, true);
    }

    /**
     * Generates a random string
     *
     * @param int $length The desired length of the string
     * @return string The random string
     */
    protected function generateRandomString(int $length) : string
    {
        // N bytes becomes 2N characters in bin2hex(), hence the division by 2
        $string = bin2hex(random_bytes(ceil($length / 2)));

        if ($length % 2 == 1) {
            // Slice off one character to make it the appropriate odd length
            $string = mb_substr($string, 1);
        }

        return $string;
    }
}
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

/**
 * Defines the interface for token factories to implement
 */
interface ITokenFactory
{
    /**
     * Creates a token
     *
     * @param int|string $userId The Id of the user that owns the token
     * @param int|string $algorithm The algorithm to use
     * @param DateTimeImmutable $validFrom The valid-from date
     * @param DateTimeImmutable $validTo The valid-to date
     * @param string $unhashedToken The unhashed token
     * @return IToken The token The token
     */
    public function createToken(
        $userId,
        $algorithm,
        DateTimeImmutable $validFrom,
        DateTimeImmutable $validTo,
        string &$unhashedToken
    ) : IToken;
}
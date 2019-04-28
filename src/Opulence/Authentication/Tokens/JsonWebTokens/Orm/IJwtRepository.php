<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Tokens\JsonWebTokens\Orm;

use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;

/**
 * Defines the interface for JWT repositories to implement
 */
interface IJwtRepository
{
    /**
     * Adds a token for a subject
     *
     * @param SignedJwt $jwt The token to add
     */
    public function add(SignedJwt $jwt): void;

    /**
     * Deletes a token
     *
     * @param SignedJwt $jwt The token to delete
     */
    public function delete(SignedJwt $jwt): void;

    /**
     * Deletes all tokens for a subject
     *
     * @param string $subjectId The Id of the subject whose tokens we're deleting
     */
    public function deleteAllForSubject(string $subjectId): void;

    /**
     * Gets all the tokens
     *
     * @return SignedJwt[] The list of all the tokens
     */
    public function getAll(): array;

    /**
     * Gets the token with the input Id
     *
     * @param int|string $id The Id of the token
     * @return SignedJwt The token
     */
    public function getById($id): SignedJwt;

    /**
     * Checks if a token exists
     *
     * @param SignedJwt $jwt The token to search for
     * @return bool True if the token exists, otherwise false
     */
    public function has(SignedJwt $jwt): bool;
}

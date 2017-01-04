<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
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
    public function add(SignedJwt $jwt);

    /**
     * Deletes a token
     *
     * @param SignedJwt $jwt The token to delete
     */
    public function delete(SignedJwt $jwt);

    /**
     * Deletes all tokens for a subject
     *
     * @param string $subjectId The Id of the subject whose tokens we're deleting
     */
    public function deleteAllForSubject(string $subjectId);

    /**
     * Gets all the tokens
     *
     * @return SignedJwt[] The list of all the tokens
     */
    public function getAll() : array;

    /**
     * Gets the token with the input Id
     *
     * @param int|string $id The Id of the token
     * @return SignedJwt The token
     */
    public function getById($id) : SignedJwt;

    /**
     * Checks if a token exists
     *
     * @param SignedJwt $jwt The token to search for
     * @return bool True if the token exists, otherwise false
     */
    public function has(SignedJwt $jwt) : bool;
}

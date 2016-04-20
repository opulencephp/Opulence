<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Credentials\Orm;

use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;

/**
 * Defines the interface for refresh token repositories to implement
 */
interface IRefreshTokenRepository
{
    /**
     * Adds a refresh token for a subject
     *
     * @param SignedJwt $refreshToken The refresh token to add
     */
    public function add(SignedJwt &$refreshToken);

    /**
     * Deletes a refresh token
     *
     * @param SignedJwt $refreshToken The refresh token to delete
     */
    public function delete(SignedJwt $refreshToken);

    /**
     * Deletes all refresh tokens for a subject
     *
     * @param string $subjectId The Id of the subject whose refresh tokens we're deleting
     */
    public function deleteAllForSubject(string $subjectId);

    /**
     * Gets all the refresh tokens
     *
     * @return SignedJwt[] The list of all the refresh tokens
     */
    public function getAll() : array;

    /**
     * Gets the refresh token with the input Id
     *
     * @param int|string $id The Id of the refresh token
     * @return SignedJwt The refresh token
     */
    public function getById($id) : SignedJwt;

    /**
     * Checks if a refresh token exists
     *
     * @param SignedJwt $refreshToken The refresh token to search for
     * @return bool True if the refresh token exists, otherwise false
     */
    public function has(SignedJwt $refreshToken) : bool;
}
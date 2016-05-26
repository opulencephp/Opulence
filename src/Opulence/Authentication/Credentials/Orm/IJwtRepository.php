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
 * Defines the interface for JWT repositories to implement
 */
interface IJwtRepository 
{
    /**
     * Adds the JWT
     * 
     * @param SignedJwt $jwt The token to add
     */
    public function add(SignedJwt &$jwt);
    
    /**
     * Deletes the JWT
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
     * Gets the token with the input Id
     * 
     * @param int|string $id The Id of the token to get
     * @return SignedJwt The token with the input Id
     */
    public function getById($id);
    
    /**
     * Gets whether or not the repository has a token
     * 
     * @param SignedJwt $jwt The token to check for
     * @return bool True if the token exists, otherwise false
     */
    public function has(SignedJwt $jwt) : bool;
}
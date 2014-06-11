<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for token repos to implement
 */
namespace RDev\Models\Cryptography\Repositories\Token;
use RDev\Models\Cryptography;
use RDev\Models\Cryptography\Repositories\Token\Exceptions as TokenExceptions;
use RDev\Models\ORM\Repositories;
use RDev\Models\ORM\Repositories\Exceptions as RepoExceptions;

interface ITokenRepo extends Repositories\IRepo
{
    /**
     * Gets all tokens for a user
     *
     * @param int $typeId The Id of the type of token we're searching for
     * @param int $userId The Id of the user whose tokens we're searching for
     * @return array|bool The list of tokens if successful, otherwise false
     */
    public function getAllByUserId($typeId, $userId);

    /**
     * Gets the token for a user that matches the unhashed value
     *
     * @param int $id The Id of the token we're searching for
     * @param int $typeId The Id of the type of token we're searching for
     * @param int $userId The Id of the user whose token we're searching for
     * @param string $unhashedValue The unhashed value we're looking for
     * @return Cryptography\Token|bool The token if successful, otherwise false
     * @throws TokenExceptions\IncorrectHashException Thrown if the unhashed value doesn't match the hashed value
     */
    public function getByIdAndUserIdAndUnhashedValue($id, $typeId, $userId, $unhashedValue);

    /**
     * Gets a token for a user, which we can do if there's only a single token of this type per user
     *
     * @param int $typeId The Id of the type of token we're searching for
     * @param int $userId The Id of the user whose tokens we're searching for
     * @return Cryptography\Token|bool The list of tokens if successful, otherwise false
     */
    public function getByUserId($typeId, $userId);

    /**
     * Gets the token for a user that matches the unhashed value
     *
     * @param int $typeId The Id of the type of token we're searching for
     * @param int $userId The Id of the user whose token we're searching for
     * @param string $unhashedValue The unhashed value we're looking for
     * @return Cryptography\Token|bool The token if successful, otherwise false
     * @throws TokenExceptions\IncorrectHashException Thrown if the unhashed value doesn't match the hashed value
     */
    public function getByUserIdAndUnhashedValue($typeId, $userId, $unhashedValue);
} 
<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for token repos to implement
 */
namespace RDev\Cryptography\ORM\Token;
use RDev\Cryptography;
use RDev\ORM\Repositories;

/**
 * @method Cryptography\Token getById($id)
 * @method Cryptography\Token[] getAll()
 */
interface IRepo extends Repositories\IRepo
{
    /**
     * Gets the token that matches the unhashed value
     *
     * @param int $id The Id of the token we're searching for
     * @param string $unhashedValue The unhashed value we're looking for
     * @return Cryptography\Token|null The token if successful, otherwise null
     * @throws IncorrectHashException Thrown if the unhashed value doesn't match the hashed value
     */
    public function getByIdAndUnhashedValue($id, $unhashedValue);
} 
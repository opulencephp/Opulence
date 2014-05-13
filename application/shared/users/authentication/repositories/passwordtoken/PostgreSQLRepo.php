<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the PostgreSQL repo for password tokens
 */
namespace RamODev\Application\Shared\Users\Authentication\Repositories\PasswordToken;
use RamODev\Application\Shared\Cryptography;
use RamODev\Application\Shared\Cryptography\Repositories\Token;
use RamODev\Application\Shared\Cryptography\Repositories\Token\Exceptions\IncorrectHashException;
use RamODev\Application\Shared\Databases\SQL;
use RamODev\Application\Shared\Databases\SQL\PostgreSQL\QueryBuilders;
use RamODev\Application\Shared\Exceptions\Log;
use RamODev\Application\Shared\Repositories;

class PostgreSQLRepo extends Repositories\PostgreSQLRepo implements IPasswordTokenRepo
{
    /** @var Token\ITokenRepo The token repo */
    private $tokenRepo = null;

    /**
     * @param SQL\Database $sqlDatabase The database to use for queries
     * @param Token\ITokenRepo $tokenRepo The token repo
     */
    public function __construct(SQL\Database $sqlDatabase, Token\ITokenRepo $tokenRepo)
    {
        parent::__construct($sqlDatabase);

        $this->tokenRepo = $tokenRepo;
    }

    /**
     * Adds a password token to the repo
     *
     * @param int $userId The Id of the user whose password we're adding
     * @param Cryptography\Token $passwordToken The token containing data about the password
     * @param string $hashedPassword The hashed password
     * @return bool True if successful, otherwise false
     */
    public function add($userId, Cryptography\Token &$passwordToken, $hashedPassword)
    {
        $this->sqlDatabase->startTransaction();

        try
        {
            // We either UPDATE or INSERT this password, depending on whether or not one was previously set
            $checkIfPasswordAlreadySetResults = $this->sqlDatabase
                ->query("SELECT id FROM authentication.passwords WHERE userid = :userId",
                    array("userId" => $userId));

            if($checkIfPasswordAlreadySetResults->hasResults())
            {
                $this->updatePassword($userId, $passwordToken);
            }
            else
            {
                $this->sqlDatabase->query("INSERT INTO authentication.passwords (userid, tokenid) VALUES (:userId, :tokenId)",
                    array("userId" => $userId, "tokenId" => $passwordToken->getId()));
                $this->log($userId, $passwordToken->getId(), Repositories\ActionTypes::ADDED);
            }

            $this->sqlDatabase->commitTransaction();

            return true;
        }
        catch(SQL\Exceptions\SQLException $ex)
        {
            Log::write("Failed to add password: " . $ex);
            $this->sqlDatabase->rollBackTransaction();
        }

        return false;
    }

    /**
     * Gets the password token for a user
     *
     * @param int $userId The Id of the user whose password token we want
     * @return Cryptography\Token|bool The password token if successful, otherwise false
     */
    public function getByUserId($userId)
    {
        try
        {
            // Get the current password
            $queryBuilders = new QueryBuilders\QueryBuilder();
            $selectQuery = $queryBuilders->select("p.tokenid")
                ->from("authentication.passwords", "p")
                ->innerJoin("authentication.tokens", "t", "t.id = p.tokenid")
                ->where("userid = :userId")
                ->andWhere("t.validfrom <= NOW()")
                ->andWhere("NOW() < t.validto")
                ->addNamedPlaceholderValue("userId", $userId);

            $results = $this->sqlDatabase->query($selectQuery->getSQL(), $selectQuery->getParameters());

            if(!$results->hasResults())
            {
                return false;
            }

            return $this->tokenRepo->getById($results->getResult(0, "tokenid"));
        }
        catch(SQL\Exceptions\SQLException $ex)
        {
            Log::write("Failed to get password token from user Id: " . $ex);
        }

        return false;
    }

    /**
     * Gets the password token for a user that matches the input unhashed password
     *
     * @param int $userId The Id of the user whose password token we want
     * @param string $unhashedPassword The unhashed password
     * @return Cryptography\Token|bool The password token if successful, otherwise false
     * @throws IncorrectHashException Thrown if the unhashed value doesn't match the hashed value
     */
    public function getByUserIdAndUnhashedPassword($userId, $unhashedPassword)
    {
        $passwordToken = $this->getByUserId($userId);

        if($passwordToken === false)
        {
            return false;
        }

        // Make sure that the password matches
        if($this->tokenRepo->getByIdAndUnhashedValue($passwordToken->getId(), $unhashedPassword) === false)
        {
            return false;
        }

        try
        {
            $results = $this->sqlDatabase->query("SELECT count(*) AS thecount FROM authentication.passwords
            WHERE userid = :userId AND tokenid = :tokenId",
                array("userId" => $userId, "tokenId" => $passwordToken->getId()));

            if(!$results->hasResults() || $results->getResult(0, "thecount") != 1)
            {
                return false;
            }

            return $passwordToken;
        }
        catch(SQL\Exceptions\SQLException $ex)
        {
            Log::write("Failed to get password token from user Id and unhashed password: " . $ex);
        }

        return false;
    }

    /**
     * Gets the hashed value for a token
     *
     * @param int $id The Id of the hash whose value we're searching for
     * @return string|bool The hashed value if successful, otherwise false
     */
    public function getHashedValue($id)
    {
        return $this->tokenRepo->getHashedValue($id);
    }

    /**
     * Updates a password token for a user in the repo
     *
     * @param int $userId The Id of the user whose password we're updating
     * @param Cryptography\Token $passwordToken The token containing data about the password
     * @param string $hashedPassword The hashed password
     * @return bool True if successful, otherwise false
     */
    public function update($userId, Cryptography\Token &$passwordToken, $hashedPassword)
    {
        return $this->add($userId, $passwordToken, $hashedPassword);
    }

    /**
     * Logs any changes to a password token
     *
     * @param int $userId The Id of the user whose password we're logging
     * @param int $tokenId The Id of the token we're logging
     * @param int $actionTypeId The Id of the type of action we're taking
     * @throws SQL\Exceptions\SQLException Thrown if there's an error with the query
     */
    private function log($userId, $tokenId, $actionTypeId)
    {
        $this->sqlDatabase->query("INSERT INTO authentication.passwordslog (userid, tokenid, actiontypeid)
        VALUES (:userId, :tokenId, :actionTypeId)", array(
            "userId" => $userId,
            "tokenId" => $tokenId,
            "actionTypeId" => $actionTypeId
        ));
    }

    /**
     * Updates the password for the input user
     *
     * @param int $userId The Id of the user whose password we're updating
     * @param Cryptography\Token $passwordToken The new password token
     * @throws SQL\Exceptions\SQLException Thrown if there's an error with the query
     */
    private function updatePassword($userId, Cryptography\Token &$passwordToken)
    {
        $this->sqlDatabase->query("UPDATE authentication.passwords SET tokenid = :tokenId WHERE userid = :userId",
            array("userId" => $userId, "tokenId" => $passwordToken->getId()));
        $this->log($userId, $passwordToken->getId(), Repositories\ActionTypes::UPDATED);
    }
} 
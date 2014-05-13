<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides methods for retrieving login credentials from a PostgreSQL database
 */
namespace RamODev\Application\Shared\Users\Authentication\Credentials\Repositories\LoginCredentials;
use RamODev\Application\Shared\Cryptography;
use RamODev\Application\Shared\Cryptography\Repositories\Token;
use RamODev\Application\Shared\Cryptography\Repositories\Token\Exceptions\IncorrectHashException;
use RamODev\Application\Shared\Databases\SQL;
use RamODev\Application\Shared\Exceptions\Log;
use RamODev\Application\Shared\Repositories;
use RamODev\Application\Shared\Users\Authentication\Credentials;
use RamODev\Application\Shared\Users\Authentication\Credentials\Factories;

class PostgreSQLRepo extends Repositories\PostgreSQLRepo implements ILoginCredentialsRepo
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
     * Adds credentials to the repo
     *
     * @param Credentials\LoginCredentials $credentials The credentials to add to the repo
     * @param string $hashedLoginTokenValue The hashed login token value
     * @return bool True if successful, otherwise false
     */
    public function add(Credentials\LoginCredentials $credentials, $hashedLoginTokenValue)
    {
        try
        {
            $this->sqlDatabase->query("INSERT INTO authentication.logintokens (userid, tokenid)
            VALUES (:userId, :tokenId)",
                array("userId" => $credentials->getUserId(), "tokenId" => $credentials->getLoginToken()->getId()));

            return true;
        }
        catch(SQL\Exceptions\SQLException $ex)
        {
            Log::write("Failed to add credentials: " . $ex);
        }

        return false;
    }

    /**
     * Deactivates the input credentials from the repo
     *
     * @param Credentials\LoginCredentials $credentials The credentials to deactivate
     * @return bool True if successful, otherwise false
     */
    public function deactivate(Credentials\LoginCredentials $credentials)
    {
        // The token is already deactivated at this point, so there's nothing to do here
        return true;
    }

    /**
     * Deactivates all the login credentials for a user
     * This is useful in such cases like password changes where we want to deactivate all old sessions
     *
     * @param int $userId The Id of the user whose credentials we are deactivating
     * @return bool True if successful, otherwise false
     */
    public function deactivateAllByUserId($userId)
    {
        /**
         * Ordinarily I'd deactivate the tokens in Repo class so that the Redis and PostgreSQL repos don't have to worry
         * However, for security's sake, I need to deactivate them in a single SQL transaction, and it'd be cleaner to
         * do that here than elsewhere
         */
        $this->sqlDatabase->startTransaction();
        $loginCredentials = $this->getAllByUserId($userId);

        /** @var Credentials\LoginCredentials $loginCredential */
        foreach($loginCredentials as $loginCredential)
        {
            $this->tokenRepo->deactivate($loginCredential->getLoginToken());
        }

        $this->sqlDatabase->commitTransaction();
    }

    /**
     * Gets a list of all the login credentials for a user
     *
     * @param int $userId The Id of the user whose login credentials we want
     * @return array|bool The list of login credentials if successful, otherwise false
     */
    public function getAllByUserId($userId)
    {
        try
        {
            $loginCredentials = array();
            $results = $this->sqlDatabase->query("SELECT tokenid FROM authentication.logintokens WHERE userid = :userId",
                array("userId" => $userId));
            $rows = $results->getAllRows();

            foreach($rows as $row)
            {
                $token = $this->tokenRepo->getById($row["tokenid"]);

                if($token === false)
                {
                    return false;
                }

                $loginCredentials[] = new Credentials\LoginCredentials($userId, $token);
            }

            return $loginCredentials;
        }
        catch(SQL\Exceptions\SQLException $ex)
        {
            Log::write("Failed to get all login credentials for user: " . $ex);
        }

        return false;
    }

    /**
     * Gets the login credentials that match the parameters
     *
     * @param int $userId The Id of the user whose credentials we are searching for
     * @param int $loginTokenId The Id of the login token we're searching for
     * @param string $unhashedLoginTokenValue The unhashed login token we are searching for
     * @return Credentials\LoginCredentials|bool The login credentials if successful, otherwise false
     * @throws IncorrectHashException Thrown if the unhashed value doesn't match the hashed value
     */
    public function getByUserIdAndLoginToken($userId, $loginTokenId, $unhashedLoginTokenValue)
    {
        $loginToken = $this->tokenRepo->getByIdAndUnhashedValue($loginTokenId, $unhashedLoginTokenValue);

        if($loginToken === false)
        {
            return false;
        }

        try
        {
            $results = $this->sqlDatabase->query("SELECT count(*) AS thecount FROM authentication.logintokens
            WHERE userid = :userId AND tokenid = :loginTokenId",
                array("userId" => $userId, "loginTokenId" => $loginTokenId));

            if(!$results->hasResults() || $results->getResult(0, "thecount") != 1)
            {
                return false;
            }

            return new Credentials\LoginCredentials($userId, $loginToken);
        }
        catch(SQL\Exceptions\SQLException $ex)
        {
            Log::write("Failed to get credentials from user Id and login token: " . $ex);
        }

        return false;
    }
} 
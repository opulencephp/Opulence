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
     * Deauthorizes the input credentials from the repo
     *
     * @param Credentials\LoginCredentials $credentials The credentials to deauthorize
     * @param string $unhashedLoginTokenValue The unhashed token value
     * @return bool True if successful, otherwise false
     * @throws IncorrectHashException Thrown if the unhashed value doesn't match the hashed value
     */
    public function deauthorize(Credentials\LoginCredentials $credentials, $unhashedLoginTokenValue)
    {
        // The token is already deauthorized at this point, so there's nothing we do here
        return true;
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
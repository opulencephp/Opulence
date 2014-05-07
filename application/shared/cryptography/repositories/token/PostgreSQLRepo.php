<?php
/**
 * Copyright (C) 2014 David Young
 *
 *
 */
namespace RamODev\Application\Shared\Cryptography\Repositories\Token;
use RamODev\Application\Shared\Cryptography;
use RamODev\Application\Shared\Databases\SQL;
use RamODev\Application\Shared\Databases\SQL\Exceptions\SQLException;
use RamODev\Application\Shared\Databases\SQL\PostgreSQL\QueryBuilders;
use RamODev\Application\Shared\Exceptions\Log;
use RamODev\Application\Shared\Repositories;

class PostgreSQLRepo extends Repositories\PostgreSQLRepo implements ITokenRepo
{
    /** @var QueryBuilders\SelectQuery The query used to select tokens */
    private $getQuery = null;
    /** @var string The IP address of the user that is calling into this repo */
    private $ipAddress = "";
    /** @var string The user agent of the user that is calling into this repo */
    private $userAgent = "";

    /**
     * @param SQL\Database $sqlDatabase The database connection to use in this repo
     * @param string $ipAddress The IP address of the user that is calling into this repo
     * @param string $userAgent The user agent of the user that is calling into this repo
     */
    public function __construct(SQL\Database $sqlDatabase, $ipAddress, $userAgent)
    {
        parent::__construct($sqlDatabase);

        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
    }

    /**
     * Adds a token to the repo
     *
     * @param Cryptography\Token $token The token we're adding
     * @param string $hashedValue The hashed token value
     * @return bool True if successful, otherwise false
     */
    public function add(Cryptography\Token &$token, $hashedValue)
    {
        $this->sqlDatabase->startTransaction();

        try
        {
            $this->sqlDatabase->query("INSERT INTO authentication.tokens (token, validfrom, validto) VALUES
            (:token, :validFrom, :validTo)", array(
                "token" => $hashedValue,
                "validFrom" => $token->getValidFrom()->format("Y-m-d H:i:s"),
                "validTo" => $token->getValidTo()->format("Y-m-d H:i:s"),
            ));
            $token->setId($this->sqlDatabase->getLastInsertId("authentication.tokens_id_seq"));
            $this->log($token->getId(), Repositories\ActionTypes::ADDED);
            $this->sqlDatabase->commitTransaction();

            return true;
        }
        catch(SQLException $ex)
        {
            Log::write("Failed to add token: " . $ex);
            $this->sqlDatabase->rollBackTransaction();
            $token->setId(-1);
        }

        return false;
    }

    /**
     * Deauthorizes a token from use
     *
     * @param Cryptography\Token $token The token to deauthorize
     * @param string $unhashedValue The unhashed value of the token, which is used to verify we're deauthorizing the
     *      correct token
     * @return bool True if successful, otherwise false
     */
    public function deauthorize(Cryptography\Token $token, $unhashedValue)
    {
        // As an added layer of security, we verify that the user is trying to deauthorize a valid token
        $hashedValue = $this->getHashedValue($token->getId());

        if($hashedValue === false || !password_verify($unhashedValue, $hashedValue))
        {
            return false;
        }

        $this->sqlDatabase->startTransaction();

        try
        {
            $this->sqlDatabase->query("UPDATE authentication.tokens SET validto = :validTo WHERE id = :id",
                array("validTo" => date("Y-m-d H:i:s", 0), "id" => $token->getId()));
            $this->log($token->getId(), Repositories\ActionTypes::DELETED);

            return true;
        }
        catch(SQLException $ex)
        {
            Log::write("Failed to deauthorize token: " . $ex);
            $this->sqlDatabase->rollBackTransaction();
        }

        return false;
    }

    /**
     * Gets a list of all the tokens
     *
     * @return array|bool The list of all the tokens if successful, otherwise false
     */
    public function getAll()
    {
        $this->buildGetQuery();

        return $this->read($this->getQuery->getSQL(), $this->getQuery->getParameters(), "createTokensFromRows", false);
    }

    /**
     * Gets the token with the input Id
     *
     * @param int $id The Id of the token we're looking for
     * @return Cryptography\Token|bool The token if successful, otherwise false
     */
    public function getById($id)
    {
        $this->buildGetQuery();
        $this->getQuery->andWhere("id = :id")
            ->addNamedPlaceholderValue("id", $id);

        return $this->read($this->getQuery->getSQL(), $this->getQuery->getParameters(), "createTokensFromRows", true);
    }

    /**
     * Gets a token by its Id and unhashed value
     *
     * @param int $id The Id of the token we're looking for
     * @param string $unhashedValue The unhashed value we're looking for
     * @return Cryptography\Token|bool The token if successful, otherwise false
     */
    public function getByIdAndUnhashedValue($id, $unhashedValue)
    {
        $tokenFromId = $this->getById($id);

        if($tokenFromId === false)
        {
            return false;
        }

        $hashedValue = $this->getHashedValue($tokenFromId->getId());

        if($hashedValue === false || !password_verify($unhashedValue, $hashedValue))
        {
            return false;
        }

        return $tokenFromId;
    }

    /**
     * Gets the hashed value for a token
     *
     * @param int $id The Id of the hash whose value we're searching for
     * @return string|bool The hashed value if successful, otherwise false
     */
    public function getHashedValue($id)
    {
        try
        {
            $results = $this->sqlDatabase->query("SELECT token from authentication.tokens WHERE id = :id",
                array("id" => $id));

            if(!$results->hasResults())
            {
                return false;
            }

            return $results->getResult(0, "token");
        }
        catch(SQLException $ex)
        {
            Log::write("Failed to get hashed token value: " . $ex);
        }

        return false;
    }

    /**
     * Creates a list of tokens from the query results
     *
     * @param array $rows The list of query results
     * @return array The list of token objects
     */
    protected function createTokensFromRows($rows)
    {
        $tokens = array();

        foreach($rows as $row)
        {
            $id = $row["id"];
            $validFrom = new \DateTime($row["validfrom"], new \DateTimeZone("UTC"));
            $validTo = new \DateTime($row["validto"], new \DateTimeZone("UTC"));

            $tokens[] = new Cryptography\Token($id, $validFrom, $validTo);
        }

        return $tokens;
    }

    /**
     * Builds the get query
     */
    private function buildGetQuery()
    {
        $queryBuilders = new QueryBuilders\QueryBuilder();
        $this->getQuery = $queryBuilders->select("id", "validfrom", "validto")
            ->from("authentication.tokens");
    }

    /**
     * Logs an event in the tokens log
     *
     * @param int $tokenId The Id of the token that has been changed
     * @param int $actionTypeId The Id of the type of action we've taken on the token
     * @throws SQLException Thrown if the query failed
     */
    private function log($tokenId, $actionTypeId)
    {
        $this->sqlDatabase->query("INSERT INTO authentication.tokenslog (tokenid, ipaddress, useragent, actiontypeid) VALUES
        (:tokenId, :ipAddress, :userAgent, :actionTypeId)", array(
            "tokenId" => $tokenId,
            "ipAddress" => $this->ipAddress,
            "userAgent" => $this->userAgent,
            "actionTypeId" => $actionTypeId
        ));
    }
} 
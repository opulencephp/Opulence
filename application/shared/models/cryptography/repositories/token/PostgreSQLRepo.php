<?php
/**
 * Copyright (C) 2014 David Young
 *
 *
 */
namespace RamODev\Application\Shared\Models\Cryptography\Repositories\Token;
use RamODev\Application\Shared\Models\Cryptography;
use RamODev\Application\Shared\Models\Cryptography\Repositories\Token\Exceptions\IncorrectHashException;
use RamODev\Application\Shared\Models\Databases\SQL;
use RamODev\Application\Shared\Models\Databases\SQL\Exceptions\SQLException;
use RamODev\Application\Shared\Models\Databases\SQL\PostgreSQL\QueryBuilders;
use RamODev\Application\Shared\Models\Exceptions\Log;
use RamODev\Application\Shared\Models\Repositories;

class PostgreSQLRepo extends Repositories\PostgreSQLRepo implements ITokenRepo
{
    /** @var QueryBuilders\SelectQuery The query used to select tokens */
    private $getQuery = null;
    /** @var string The IP address of the user that is calling into this repo */
    private $ipAddress = "";
    /** @var string The user agent of the user that is calling into this repo */
    private $userAgent = "";

    /**
     * @param SQL\SQL $sql The SQL object to use in this repo
     * @param string $ipAddress The IP address of the user that is calling into this repo
     * @param string $userAgent The user agent of the user that is calling into this repo
     */
    public function __construct(SQL\SQL $sql, $ipAddress, $userAgent)
    {
        parent::__construct($sql);

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
        try
        {
            $this->sql
                ->query("INSERT INTO users.tokens (token, tokentypeid, userid, validfrom, validto, useragent, ipaddress)
VALUES (:token, :tokenTypeId, :userId, :validFrom, :validTo, :userAgent, :ipAddress)", array(
                    "token" => $hashedValue,
                    "tokenTypeId" => $token->getTypeId(),
                    "userId" => $token->getUserId(),
                    "validFrom" => $token->getValidFrom()->format("Y-m-d H:i:s"),
                    "validTo" => $token->getValidTo()->format("Y-m-d H:i:s"),
                    "userAgent" => $this->userAgent,
                    "ipAddress" => $this->ipAddress
                ));
            $token->setId((int)$this->sql->lastInsertID("users.tokens_id_seq"));

            return true;
        }
        catch(SQLException $ex)
        {
            Log::write("Failed to add token: " . $ex);
        }

        return false;
    }

    /**
     * Deactivates a token from use
     *
     * @param Cryptography\Token $token The token to deactivate
     * @return bool True if successful, otherwise false
     */
    public function deactivate(Cryptography\Token &$token)
    {
        try
        {
            $this->sql->query("UPDATE users.tokens SET isactive = 'f' WHERE id = :id",
                array("id" => $token->getId()));

            return true;
        }
        catch(SQLException $ex)
        {
            Log::write("Failed to deactivate token: " . $ex);
        }

        return false;
    }

    /**
     * Deactivates all tokens for a user
     *
     * @param int $typeId The Id of the type of token we're deactivating
     * @param int $userId The Id of the user whose tokens we're deactivating
     * @return bool True if successful, otherwise false
     */
    public function deactivateAllByUserId($typeId, $userId)
    {
        try
        {
            $this->sql
                ->query("UPDATE users.tokens SET isactive = 'f' WHERE tokentypeid = :typeId AND userid = :userId",
                    array(
                        "typeId" => $typeId,
                        "userId" => $userId
                    ));

            return true;
        }
        catch(SQLException $ex)
        {
            Log::write("Failed to deactivate all tokens for a user: " . $ex);
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

        return $this->read($this->getQuery->getSQL(), $this->getQuery->getParameters(), false);
    }

    /**
     * Gets all tokens for a user
     *
     * @param int $typeId The Id of the type of token we're searching for
     * @param int $userId The Id of the user whose tokens we're searching for
     * @return array|bool The list of tokens if successful, otherwise false
     */
    public function getAllByUserId($typeId, $userId)
    {
        $this->buildGetQuery();
        $this->getQuery->andWhere("tokentypeid = :typeId")
            ->andWhere("userid = :userId")
            ->addNamedPlaceholderValues(array(
                "typeId" => $typeId,
                "userId" => $userId
            ));

        return $this->read($this->getQuery->getSQL(), $this->getQuery->getParameters(), false);
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

        return $this->read($this->getQuery->getSQL(), $this->getQuery->getParameters(), true);
    }

    /**
     * Gets the token for a user that matches the unhashed value
     *
     * @param int $id The Id of the token we're searching for
     * @param int $typeId The Id of the type of token we're searching for
     * @param int $userId The Id of the user whose token we're searching for
     * @param string $unhashedValue The unhashed value we're looking for
     * @return Cryptography\Token|bool The token if successful, otherwise false
     * @throws IncorrectHashException Thrown if the unhashed value doesn't match the hashed value
     */
    public function getByIdAndUserIdAndUnhashedValue($id, $typeId, $userId, $unhashedValue)
    {
        $tokenFromId = $this->getById($id);

        if($tokenFromId === false || $tokenFromId->getUserId() !== $userId || $tokenFromId->getTypeId() !== $typeId)
        {
            return false;
        }

        $hashedValue = $this->getHashedValue($tokenFromId->getId());

        if($hashedValue === false)
        {
            return false;
        }

        if(!password_verify($unhashedValue, $hashedValue))
        {
            throw new IncorrectHashException("Incorrect hash");
        }

        return $tokenFromId;
    }

    /**
     * Gets a token for a user, which we can do if there's only a single token of this type per user
     *
     * @param int $typeId The Id of the type of token we're searching for
     * @param int $userId The Id of the user whose tokens we're searching for
     * @return Cryptography\Token|bool The list of tokens if successful, otherwise false
     */
    public function getByUserId($typeId, $userId)
    {
        $this->buildGetQuery();
        $this->getQuery->andWhere("tokentypeid = :typeId")
            ->andWhere("userid = :userId")
            ->addNamedPlaceholderValues(array(
                "typeId" => $typeId,
                "userId" => $userId
            ));

        return $this->read($this->getQuery->getSQL(), $this->getQuery->getParameters(), true);
    }

    /**
     * Gets the token for a user that matches the unhashed value
     *
     * @param int $typeId The Id of the type of token we're searching for
     * @param int $userId The Id of the user whose token we're searching for
     * @param string $unhashedValue The unhashed value we're looking for
     * @return Cryptography\Token|bool The token if successful, otherwise false
     * @throws IncorrectHashException Thrown if the unhashed value doesn't match the hashed value
     */
    public function getByUserIdAndUnhashedValue($typeId, $userId, $unhashedValue)
    {
        $this->buildGetQuery();
        $this->getQuery->andWhere("userid = :userId")
            ->andWhere("tokentypeid = :typeId")
            ->addNamedPlaceholderValues(array(
                "userId" => $userId,
                "typeId" => $typeId
            ));

        $tokenFromUserId = $this->read($this->getQuery->getSQL(), $this->getQuery->getParameters(), true);

        if($tokenFromUserId === false)
        {
            return false;
        }

        $hashedValue = $this->getHashedValue($tokenFromUserId->getId());

        if($hashedValue === false)
        {
            return false;
        }

        if(!password_verify($unhashedValue, $hashedValue))
        {
            throw new IncorrectHashException("Incorrect hash");
        }

        return $tokenFromUserId;
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
            $statement = $this->sql->query("SELECT token from users.tokens WHERE id = :id", array("id" => $id));

            if($statement->rowCount() == 0)
            {
                return false;
            }

            return $statement->fetchAll(\PDO::FETCH_ASSOC)[0]["token"];
        }
        catch(SQLException $ex)
        {
            Log::write("Failed to get hashed token value: " . $ex);
        }

        return false;
    }

    /**
     * Loads an entity from a row of data
     *
     * @param array $row The row of data
     * @return Cryptography\Token The entity
     */
    protected function loadEntity(array $row)
    {
        return new Cryptography\Token(
            $row["id"],
            $row["tokentypeid"],
            $row["userid"],
            new \DateTime($row["validfrom"], new \DateTimeZone("UTC")),
            new \DateTime($row["validto"], new \DateTimeZone("UTC")),
            $row["isactive"] == "t"
        );
    }

    /**
     * Builds the get query
     */
    private function buildGetQuery()
    {
        $queryBuilders = new QueryBuilders\QueryBuilder();
        $this->getQuery = $queryBuilders->select("id", "tokentypeid", "userid", "validfrom", "validto", "isactive")
            ->from("users.tokens");
    }
} 
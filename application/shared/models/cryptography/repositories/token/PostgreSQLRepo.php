<?php
/**
 * Copyright (C) 2014 David Young
 *
 *
 */
namespace RDev\Application\Shared\Models\Cryptography\Repositories\Token;
use RDev\Application\Shared\Models\Cryptography;
use RDev\Application\Shared\Models\Cryptography\Repositories\Token\Exceptions\IncorrectHashException;
use RDev\Application\Shared\Models\Databases\SQL;
use RDev\Application\Shared\Models\Databases\SQL\Exceptions\SQLException;
use RDev\Application\Shared\Models\Databases\SQL\PostgreSQL\QueryBuilders;
use RDev\Application\Shared\Models\Exceptions\Log;
use RDev\Application\Shared\Models\Repositories;

class PostgreSQLRepo extends Repositories\PostgreSQLRepo implements ITokenRepo
{
    /** @var QueryBuilders\SelectQuery The query used to select tokens */
    private $getQuery = null;
    /** @var string The IP address of the user that is calling into this repo */
    private $ipAddress = "";
    /** @var string The user agent of the user that is calling into this repo */
    private $userAgent = "";

    /**
     * @param SQL\RDevPDO $rDevPDO The RDevPDO object to use in this repo
     * @param string $ipAddress The IP address of the user that is calling into this repo
     * @param string $userAgent The user agent of the user that is calling into this repo
     */
    public function __construct(SQL\RDevPDO $rDevPDO, $ipAddress, $userAgent)
    {
        parent::__construct($rDevPDO);

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
            $queryBuilder = new QueryBuilders\QueryBuilder();
            $insertQuery = $queryBuilder->insert("users.tokens", array(
                "token" => $hashedValue,
                "tokentypeid" => array($token->getTypeId(), \PDO::PARAM_INT),
                "userid" => array($token->getUserId(), \PDO::PARAM_INT),
                "validfrom" => $token->getValidFrom()->format("Y-m-d H:i:s"),
                "validto" => $token->getValidTo()->format("Y-m-d H:i:s"),
                "useragent" => $this->userAgent,
                "ipaddress" => $this->ipAddress
            ));
            $statement = $this->rDevPDO->prepare($insertQuery->getSQL());
            $statement->bindValues($insertQuery->getParameters());
            $statement->execute();
            $token->setId((int)$this->rDevPDO->lastInsertID("users.tokens_id_seq"));

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
            $statement = $this->rDevPDO->prepare("UPDATE users.tokens SET isactive = 'f' WHERE id = :id");
            $statement->bindValue("id", $token->getId(), \PDO::PARAM_INT);
            $statement->execute();

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
            $statement = $this->rDevPDO
                ->prepare("UPDATE users.tokens SET isactive = 'f' WHERE tokentypeid = :typeId AND userid = :userId");
            $statement->bindValues(array(
                "typeId" => array($typeId, \PDO::PARAM_INT),
                "userId" => array($userId, \PDO::PARAM_INT)
            ));
            $statement->execute();

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
                "typeId" => array($typeId, \PDO::PARAM_INT),
                "userId" => array($userId, \PDO::PARAM_INT)
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
            ->addNamedPlaceholderValue("id", $id, \PDO::PARAM_INT);

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
                "typeId" => array($typeId, \PDO::PARAM_INT),
                "userId" => array($userId, \PDO::PARAM_INT)
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
                "userId" => array($userId, \PDO::PARAM_INT),
                "typeId" => array($typeId, \PDO::PARAM_INT)
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
            $statement = $this->rDevPDO->prepare("SELECT token from users.tokens WHERE id = :id");
            $statement->bindValue("id", $id, \PDO::PARAM_INT);
            $statement->execute();

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
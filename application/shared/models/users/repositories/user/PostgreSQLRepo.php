<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides methods for retrieving user data from a PostgreSQL database
 */
namespace RamODev\Application\Shared\Models\Users\Repositories\User;
use RamODev\Application\Shared\Models\Cryptography;
use RamODev\Application\Shared\Models\Cryptography\Repositories\Token;
use RamODev\Application\Shared\Models\Cryptography\Repositories\Token\Exceptions\IncorrectHashException;
use RamODev\Application\Shared\Models\Databases\SQL;
use RamODev\Application\Shared\Models\Databases\SQL\Exceptions as SQLExceptions;
use RamODev\Application\Shared\Models\Databases\SQL\PostgreSQL\QueryBuilders;
use RamODev\Application\Shared\Models\Databases\SQL\QueryBuilders\Exceptions as QueryBuilderExceptions;
use RamODev\Application\Shared\Models\Exceptions as SharedExceptions;
use RamODev\Application\Shared\Models\Repositories;
use RamODev\Application\Shared\Models\Users;
use RamODev\Application\Shared\Models\Users\Factories;

class PostgreSQLRepo extends Repositories\PostgreSQLRepo implements IUserRepo
{
    /** @var Factories\UserFactory The user factory to use when creating user objects */
    private $userFactory = null;
    /** @var Token\ITokenRepo The password token repo */
    private $passwordTokenRepo = null;
    /** @var QueryBuilders\SelectQuery The select query used across get methods */
    private $getQuery = null;

    /**
     * @param SQL\SQL $sql The SQL object to use for queries
     * @param Factories\UserFactory $userFactory The user factory to use when creating user objects
     * @param Token\ITokenRepo $passwordTokenRepo The password token repo
     */
    public function __construct(SQL\SQL $sql, Factories\UserFactory $userFactory, Token\ITokenRepo $passwordTokenRepo)
    {
        parent::__construct($sql);

        $this->userFactory = $userFactory;
        $this->passwordTokenRepo = $passwordTokenRepo;
    }

    /**
     * Adds a user to the repository
     *
     * @param Users\IUser $user The user to store in the repository
     * @param Cryptography\Token $passwordToken The password token
     * @param string $hashedPassword The hashed password
     * @return bool True if successful, otherwise false
     */
    public function add(Users\IUser &$user, Cryptography\Token &$passwordToken, $hashedPassword)
    {
        $this->sql->beginTransaction();

        try
        {
            // Add the user to the users table
            $queryBuilder = new QueryBuilders\QueryBuilder();
            $userInsertQuery = $queryBuilder->insert("users.users", array("username" => $user->getUsername()));
            $statement = $this->sql->prepare($userInsertQuery->getSQL());
            $statement->execute($userInsertQuery->getParameters());

            // We'll take this opportunity to set the user's actually Id
            $user->setId((int)$this->sql->lastInsertID("users.users_id_seq"));

            // Build up the insert queries to store all the user's data
            $userDataColumnMappings = array(
                array("userdatatypeid" => UserDataTypes::EMAIL, "value" => $user->getEmail()),
                array("userdatatypeid" => UserDataTypes::FIRST_NAME, "value" => $user->getFirstName()),
                array("userdatatypeid" => UserDataTypes::LAST_NAME, "value" => $user->getLastName()),
                array("userdatatypeid" => UserDataTypes::DATE_CREATED, "value" => $user->getDateCreated()->format("Y-m-d H:i:s"))
            );

            // Execute queries to insert the user data
            foreach($userDataColumnMappings as $userDataColumnMapping)
            {
                $userDataInsertQuery = $queryBuilder->insert("users.userdata", array_merge(array("userid" => $user->getId()), $userDataColumnMapping));
                $statement = $this->sql->prepare($userDataInsertQuery->getSQL());
                $statement->execute($userDataInsertQuery->getParameters());
                $this->log($user->getId(), $userDataColumnMapping["userdatatypeid"], $userDataColumnMapping["value"], Repositories\ActionTypes::ADDED);
            }

            $this->sql->commit();

            return true;
        }
        catch(SQLExceptions\SQLException $ex)
        {
            SharedExceptions\Log::write("Failed to update user: " . $ex);
            $this->sql->rollBack();
            $user->setId(-1);
        }

        return false;
    }

    /**
     * Gets all the users in the repository
     *
     * @return array|bool The array of users if successful, otherwise false
     * @throws SharedExceptions\InvalidInputException Thrown if we're expecting a single result, but we didn't get one
     */
    public function getAll()
    {
        $this->buildGetQuery();

        return $this->read($this->getQuery->getSQL(), $this->getQuery->getParameters(), false);
    }

    /**
     * Gets the user with the input email
     *
     * @param string $email The email we're searching for
     * @return Users\IUser|bool The user that has the input email if successful, otherwise false
     * @throws SharedExceptions\InvalidInputException Thrown if we're expecting a single result, but we didn't get one
     */
    public function getByEmail($email)
    {
        try
        {
            $this->buildGetQuery();
            $this->getQuery->andWhere("LOWER(email) = :email")
                ->addNamedPlaceholderValue("email", strtolower($email));

            return $this->read($this->getQuery->getSQL(), $this->getQuery->getParameters(), true);
        }
        catch(QueryBuilderExceptions\InvalidQueryException $ex)
        {
            SharedExceptions\Log::write("Invalid query: " . $ex);
        }

        return false;
    }

    /**
     * Gets the user with the input Id
     *
     * @param int $id The database Id of the user we're searching for
     * @return Users\IUser|bool The user with the input Id if successful, otherwise false
     * @throws SharedExceptions\InvalidInputException Thrown if we're expecting a single result, but we didn't get one
     */
    public function getById($id)
    {
        try
        {
            $this->buildGetQuery();
            $this->getQuery->andWhere("id = :id")
                ->addNamedPlaceholderValue("id", $id);

            return $this->read($this->getQuery->getSQL(), $this->getQuery->getParameters(), true);
        }
        catch(QueryBuilderExceptions\InvalidQueryException $ex)
        {
            SharedExceptions\Log::write("Invalid query: " . $ex);
        }

        return false;
    }

    /**
     * Gets the user with the input username
     *
     * @param string $username The username to search for
     * @return Users\IUser|bool The user with the input username if successful, otherwise false
     * @throws SharedExceptions\InvalidInputException Thrown if we're expecting a single result, but we didn't get one
     */
    public function getByUsername($username)
    {
        try
        {
            $this->buildGetQuery();
            $this->getQuery->andWhere("LOWER(username) = :username")
                ->addNamedPlaceholderValue("username", strtolower($username));

            return $this->read($this->getQuery->getSQL(), $this->getQuery->getParameters(), true);
        }
        catch(QueryBuilderExceptions\InvalidQueryException $ex)
        {
            SharedExceptions\Log::write("Invalid query: " . $ex);
        }

        return false;
    }

    /**
     * Gets the user with the input username and hashed password
     *
     * @param string $username The username to search for
     * @param string $unhashedPassword The unhashed password to search for
     * @return Users\IUser|bool The user with the input username and password if successful, otherwise false
     * @throws IncorrectHashException Thrown if we're expecting a single result, but we didn't get one
     */
    public function getByUsernameAndPassword($username, $unhashedPassword)
    {
        $userFromUsername = $this->getByUsername($username);

        if($userFromUsername === false)
        {
            return false;
        }

        $passwordToken = $this->passwordTokenRepo->getByUserIdAndUnhashedValue(
            Cryptography\TokenTypes::PASSWORD,
            $userFromUsername->getId(),
            $unhashedPassword
        );

        if($passwordToken === false)
        {
            return false;
        }

        return $userFromUsername;
    }

    /**
     * Updates a user's email address in the repository
     *
     * @param Users\IUser $user The user to update in the repository
     * @param string $email The new email address
     * @return bool True if successful, otherwise false
     * @throws SharedExceptions\InvalidInputException Thrown if we're expecting a single result, but we didn't get one
     */
    public function updateEmail(Users\IUser &$user, $email)
    {
        return $this->update($user->getId(), UserDataTypes::EMAIL, $email);
    }

    /**
     * Loads an entity from a row of data
     *
     * @param array $row The row of data
     * @return Users\User The entity
     */
    protected function loadEntity(array $row)
    {
        return new Users\User(
            $row["id"],
            $row["username"],
            $row["email"],
            new \DateTime($row["datecreated"], new \DateTimeZone("UTC")),
            $row["firstname"],
            $row["lastname"]
        );
    }

    /**
     * Builds the basic get query that's common to all get methods
     */
    private function buildGetQuery()
    {
        $queryBuilder = new QueryBuilders\QueryBuilder();
        $this->getQuery = $queryBuilder->select("id", "username", "email", "datecreated", "firstname", "lastname")
            ->from("users.usersview");
    }

    /**
     * Logs changes in the appropriate table
     *
     * @param int $userId The Id of the user whose changes we must log
     * @param int $userDataTypeId The Id of the data type whose value we're logging
     * @param mixed $value The new value of the user data
     * @param int $actionTypeId The Id of the type of action we've taken on the user
     * @throws SQLExceptions\SQLException Thrown if any of the queries fails
     */
    private function log($userId, $userDataTypeId, $value, $actionTypeId)
    {
        $queryBuilder = new QueryBuilders\QueryBuilder();
        $insertQuery = $queryBuilder->insert("users.userdatalog", array(
            "userid" => $userId,
            "userdatatypeid" => $userDataTypeId,
            "value" => $value,
            "actiontypeid" => $actionTypeId
        ));
        $statement = $this->sql->prepare($insertQuery->getSQL());
        $statement->execute($insertQuery->getParameters());
    }

    /**
     * Updates user data in the database
     *
     * @param int $userId The Id of the user whose changes we are changing
     * @param int $userDataTypeId The Id of the data type whose value we're changing
     * @param mixed $value The new value of the user data
     * @return bool True if successful, otherwise false
     */
    private function update($userId, $userDataTypeId, $value)
    {
        $this->sql->beginTransaction();

        try
        {
            $queryBuilder = new QueryBuilders\QueryBuilder();
            $updateQuery = $queryBuilder->update("users.userdata", "", array("userdatatypeid" => $userDataTypeId, "value" => $value))
                ->where("userid = ?")
                ->addUnnamedPlaceholderValue($userId);
            $statement = $this->sql->prepare($updateQuery->getSQL());
            $statement->execute($updateQuery->getParameters());
            $this->log($userId, $userDataTypeId, $value, Repositories\ActionTypes::UPDATED);
            $this->sql->commit();

            return true;
        }
        catch(SQLExceptions\SQLException $ex)
        {
            SharedExceptions\Log::write("Failed to update user: " . $ex);
            $this->sql->rollBack();
        }
        catch(QueryBuilderExceptions\InvalidQueryException $ex)
        {
            SharedExceptions\Log::write("Invalid query: " . $ex);
            $this->sql->rollBack();
        }

        return false;
    }
} 
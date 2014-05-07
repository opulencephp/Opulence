<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides methods for retrieving user data from a PostgreSQL database
 */
namespace RamODev\Application\Shared\Users\Repositories\User;
use RamODev\Application\Shared\Databases\SQL;
use RamODev\Application\Shared\Databases\SQL\Exceptions as SQLExceptions;
use RamODev\Application\Shared\Databases\SQL\PostgreSQL\QueryBuilders;
use RamODev\Application\Shared\Databases\SQL\QueryBuilders\Exceptions as QueryBuilderExceptions;
use RamODev\Application\Shared\Exceptions as SharedExceptions;
use RamODev\Application\Shared\Repositories;
use RamODev\Application\Shared\Users;
use RamODev\Application\Shared\Users\Factories;

class PostgreSQLRepo extends Repositories\PostgreSQLRepo implements IUserRepo
{
    /** @var Factories\IUserFactory The user factory to use when creating user objects */
    private $userFactory = null;
    /** @var QueryBuilders\SelectQuery The select query used across get methods */
    private $getQuery = null;

    /**
     * @param SQL\Database $sqlDatabase The database to use for queries
     * @param Factories\IUserFactory $userFactory The user factory to use when creating user objects
     */
    public function __construct(SQL\Database $sqlDatabase, Factories\IUserFactory $userFactory)
    {
        parent::__construct($sqlDatabase);

        $this->userFactory = $userFactory;
    }

    /**
     * Adds a user to the repository
     *
     * @param Users\IUser $user The user to store in the repository
     * @param string $hashedPassword The hashed password
     * @return bool True if successful, otherwise false
     */
    public function add(Users\IUser &$user, $hashedPassword)
    {
        $this->sqlDatabase->startTransaction();

        try
        {
            // Add the user to the users table
            $queryBuilder = new QueryBuilders\QueryBuilder();
            $userInsertQuery = $queryBuilder->insert("users.users", array("username" => $user->getUsername()));
            $this->sqlDatabase->query($userInsertQuery->getSQL(), $userInsertQuery->getParameters());

            // We'll take this opportunity to set the user's actually Id
            $user->setId((int)$this->sqlDatabase->getLastInsertId("users.users_id_seq"));

            // Build up the insert queries to store all the user's data
            $userDataColumnMappings = array(
                array("userdatatypeid" => UserDataTypes::EMAIL, "value" => $user->getEmail()),
                array("userdatatypeid" => UserDataTypes::PASSWORD, "value" => $hashedPassword),
                array("userdatatypeid" => UserDataTypes::FIRST_NAME, "value" => $user->getFirstName()),
                array("userdatatypeid" => UserDataTypes::LAST_NAME, "value" => $user->getLastName()),
                array("userdatatypeid" => UserDataTypes::DATE_CREATED, "value" => $user->getDateCreated()->format("Y-m-d H:i:s"))
            );

            // Execute queries to insert the user data
            foreach($userDataColumnMappings as $userDataColumnMapping)
            {
                $userDataInsertQuery = $queryBuilder->insert("users.userdata", array_merge(array("userid" => $user->getId()), $userDataColumnMapping));
                $this->sqlDatabase->query($userDataInsertQuery->getSQL(), $userDataInsertQuery->getParameters());
                $this->log($user->getId(), $userDataColumnMapping["userdatatypeid"], $userDataColumnMapping["value"], Repositories\ActionTypes::ADDED);
            }

            $this->sqlDatabase->commitTransaction();

            return true;
        }
        catch(SQLExceptions\SQLException $ex)
        {
            SharedExceptions\Log::write("Failed to update user: " . $ex);
            $this->sqlDatabase->rollBackTransaction();
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

        return $this->read($this->getQuery->getSQL(), $this->getQuery->getParameters(), "createUsersFromRows", false);
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

            return $this->read($this->getQuery->getSQL(), $this->getQuery->getParameters(), "createUsersFromRows", true);
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

            return $this->read($this->getQuery->getSQL(), $this->getQuery->getParameters(), "createUsersFromRows", true);
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

            return $this->read($this->getQuery->getSQL(), $this->getQuery->getParameters(), "createUsersFromRows", true);
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
     * @throws SharedExceptions\InvalidInputException Thrown if we're expecting a single result, but we didn't get one
     */
    public function getByUsernameAndPassword($username, $unhashedPassword)
    {
        $user = $this->getByUsername($username);

        if($user === false)
        {
            return false;
        }

        $unhashedPassword = $this->getHashedPassword($user->getId());

        if($unhashedPassword === false || !password_verify($unhashedPassword, $unhashedPassword))
        {
            return false;
        }

        return $user;
    }

    /**
     * Gets a user's hashed password from the repo
     *
     * @param int $id The ID of the user whose password we are searching for
     * @return string|bool The hashed password if successful, otherwise false
     */
    public function getHashedPassword($id)
    {
        try
        {
            $results = $this->sqlDatabase->query("SELECT password FROM users.usersview WHERE id = :userId",
                array("userId" => $id));

            if(!$results->hasResults())
            {
                return false;
            }

            return $results->getResult(0, "password");
        }
        catch(SQLExceptions\SQLException $ex)
        {
            SharedExceptions\Log::write("Unable to query user password: " . $ex);
        }

        return false;
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
     * Updates a user's password in the repository
     *
     * @param Users\IUser $user The user to update in the repository
     * @param string $hashedPassword The hashed new password
     * @return bool True if successful, otherwise false
     * @throws SharedExceptions\InvalidInputException Thrown if we're expecting a single result, but we didn't get one
     */
    public function updatePassword(Users\IUser &$user, $hashedPassword)
    {
        return $this->update($user->getId(), UserDataTypes::PASSWORD, $hashedPassword);
    }

    /**
     * Creates a list of user objects from the database results
     *
     * @param array $rows The rows of results from the query
     * @return array The list of user objects
     */
    protected function createUsersFromRows($rows)
    {
        $users = array();

        foreach($rows as $row)
        {
            $id = $row["id"];
            $username = $row["username"];
            $email = $row["email"];
            $dateCreated = new \DateTime($row["datecreated"], new \DateTimeZone("UTC"));
            $firstName = $row["firstname"];
            $lastName = $row["lastname"];

            $users[] = $this->userFactory->createUser($id, $username, $email, $dateCreated, $firstName, $lastName);
        }

        return $users;
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
        $this->sqlDatabase->query($insertQuery->getSQL(), $insertQuery->getParameters());
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
        $this->sqlDatabase->startTransaction();

        try
        {
            $queryBuilder = new QueryBuilders\QueryBuilder();
            $updateQuery = $queryBuilder->update("users.userdata", "", array("userdatatypeid" => $userDataTypeId, "value" => $value))
                ->where("userid = ?")
                ->addUnnamedPlaceholderValue($userId);
            $this->sqlDatabase->query($updateQuery->getSQL(), $updateQuery->getParameters());
            $this->log($userId, $userDataTypeId, $value, Repositories\ActionTypes::UPDATED);
            $this->sqlDatabase->commitTransaction();

            return true;
        }
        catch(SQLExceptions\SQLException $ex)
        {
            SharedExceptions\Log::write("Failed to update user: " . $ex);
            $this->sqlDatabase->rollBackTransaction();
        }
        catch(QueryBuilderExceptions\InvalidQueryException $ex)
        {
            SharedExceptions\Log::write("Invalid query: " . $ex);
            $this->sqlDatabase->rollBackTransaction();
        }

        return false;
    }
} 
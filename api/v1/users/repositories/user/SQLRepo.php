<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides methods for retrieving user data from a SQL database
 */
namespace RamODev\API\V1\Users\Repositories\User;
use RamODev\API\V1\Users;
use RamODev\API\V1\Users\Factories;
use RamODev\Databases\SQL;
use RamODev\Databases\SQL\PostgreSQL\QueryBuilders;
use RamODev\Databases\SQL\Exceptions;
use RamODev\Repositories;

require_once(__DIR__ . "/../../../../../repositories/SQLRepo.php");

class SQLRepo extends Repositories\SQLRepo implements IUserRepo
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
     * Creates a user in the repository
     *
     * @param Users\IUser $user The user to store in the repository
     * @return bool True if successful, otherwise false
     */
    public function create(Users\IUser &$user)
    {

    }

    /**
     * Gets all the users in the repository
     *
     * @return array|bool The array of users if successful, otherwise false
     */
    public function getAll()
    {
        $this->buildGetQuery();
    }

    /**
     * Gets the user with the input email
     *
     * @param string $email The email we're searching for
     * @return Users\IUser|bool The user that has the input email if successful, otherwise false
     */
    public function getByEmail($email)
    {
        $this->buildGetQuery();
        $this->getQuery->andWhere("LOWER(email) = :email")
            ->addNamedPlaceholderValue("email", strtolower($email));

        return $this->get();
    }

    /**
     * Gets the user with the input ID
     *
     * @param int $id The ID of the user we're searching for
     * @return Users\IUser|bool The user with the input ID if successful, otherwise false
     */
    public function getByID($id)
    {
        $this->buildGetQuery();
    }

    /**
     * Gets the user with the input username
     *
     * @param string $username The username to search for
     * @return Users\IUser|bool The user with the input username if successful, otherwise false
     */
    public function getByUsername($username)
    {
        $this->buildGetQuery();
    }

    /**
     * Gets the user with the input username and hashed password
     *
     * @param string $username The username to search for
     * @param string $hashedPassword The hashed password to search for
     * @return Users\IUser|bool The user with the input username and password if successful, otherwise false
     */
    public function getByUsernameAndPassword($username, $hashedPassword)
    {
        $this->buildGetQuery();
    }

    /**
     * Updates a user in the repository
     *
     * @param Users\IUser $user The user to update in the repository
     * @return bool True if successful, otherwise false
     */
    public function update(Users\IUser &$user)
    {

    }

    /**
     * Builds the basic get query that's common to all get methods
     */
    protected function buildGetQuery()
    {
        require_once(__DIR__ . "/../../../../../databases/sql/postgresql/querybuilders/QueryBuilder.php");
        $queryBuilder = new QueryBuilders\QueryBuilder();
        $this->getQuery = $queryBuilder->select("id", "username", "password", "email", "datecreated", "firstname", "lastname")
            ->from("users.usersview");
    }

    /**
     * Runs the get query and returns results, if there are any
     *
     * @return array|Users\IUser|bool The list of users or the individual user returned by the query if successful, otherwise false
     */
    protected function get()
    {
        $results = $this->sqlDatabase->query($this->getQuery->getSQL(), $this->getQuery->getParameters());

        if($results->getNumResults() == 0)
        {
            return false;
        }

        $users = array();

        while($row = $results->getRow())
        {
            $id = $row["id"];
            $username = $row["username"];
            $password = $row["password"];
            $email = $row["email"];
            $dateCreated = new \DateTime($row["datecreated"], new \DateTimeZone("UTC"));
            $firstName = $row["firstname"];
            $lastName = $row["lastname"];

            $users[] = $this->userFactory->createUser($id, $username, $password, $email, $dateCreated, $firstName, $lastName);
        }

        return $users;
    }
} 
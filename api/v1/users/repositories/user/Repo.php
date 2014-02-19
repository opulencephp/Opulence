<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides methods for retrieving user data from the repository
 */
namespace RamODev\API\V1\Users\Repositories\User;
use RamODev\API\V1\Users;
use RamODev\API\V1\Users\Factories;
use RamODev\Databases\NoSQL\Redis;
use RamODev\Databases\SQL;
use RamODev\Repositories;

require_once(__DIR__ . "/../../../../../repositories/Repo.php");
require_once(__DIR__ . "/../../User.php");
require_once(__DIR__ . "/IUserRepo.php");
require_once(__DIR__ . "/NoSQLRepo.php");
require_once(__DIR__ . "/SQLRepo.php");
require_once(__DIR__ . "/UserDataTypes.php");

class Repo extends Repositories\Repo implements IUserRepo
{
    /** @var Factories\IUserFactory The user factory to use when creating user objects */
    private $userFactory = null;

    /**
     * @param Redis\Database $redisDatabase The NoSQL database used in the repo
     * @param SQL\Database $sqlDatabase The relational database used in the repo
     * @param Factories\IUserFactory $userFactory The user factory to use when creating user objects
     */
    public function __construct(Redis\Database $redisDatabase, SQL\Database $sqlDatabase, Factories\IUserFactory $userFactory)
    {
        $this->userFactory = $userFactory;

        parent::__construct($redisDatabase, $sqlDatabase);
    }

    /**
     * Creates a user in the repository
     *
     * @param Users\IUser $user The user to store in the repository
     * @return bool True if successful, otherwise false
     */
    public function create(Users\IUser &$user)
    {
        return $this->set(__FUNCTION__, array(&$user));
    }

    /**
     * Gets all the users in the repository
     *
     * @return array|bool The array of users if successful, otherwise false
     */
    public function getAll()
    {
        return $this->get(__FUNCTION__);
    }

    /**
     * Gets the user with the input email
     *
     * @param string $email The email we're searching for
     * @return Users\IUser|bool The user that has the input email if successful, otherwise false
     */
    public function getByEmail($email)
    {
        return $this->get(__FUNCTION__, array($email));
    }

    /**
     * Gets the user with the input ID
     *
     * @param int $id The ID of the user we're searching for
     * @return Users\IUser|bool The user with the input ID if successful, otherwise false
     */
    public function getByID($id)
    {
        return $this->get(__FUNCTION__, array($id));
    }

    /**
     * Gets the user with the input username
     *
     * @param string $username The username to search for
     * @return Users\IUser|bool The user with the input username if successful, otherwise false
     */
    public function getByUsername($username)
    {
        return $this->get(__FUNCTION__, array($username));
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
        return $this->get(__FUNCTION__, array($username, $hashedPassword));
    }

    /**
     * Updates a user in the repository
     *
     * @param Users\IUser $user The user to update in the repository
     * @return bool True if successful, otherwise false
     */
    public function update(Users\IUser &$user)
    {
        return $this->set(__FUNCTION__, array(&$user));
    }

    /**
     * Stores a user object that wasn't initially found in the NoSQL repo
     *
     * @param Users\IUser $user The user to store in the NoSQL repo
     */
    protected function addDataToNoSQLRepo(&$user)
    {
        $this->noSQLRepo->create($user);
    }

    /**
     * Gets a NoSQL repo to use in this repo
     *
     * @param Redis\Database $redisDatabase The NoSQL database used in the repo
     * @return NoSQLRepo The NoSQL repo to use
     */
    protected function getNoSQLRepo(Redis\Database $redisDatabase)
    {
        return new NoSQLRepo($redisDatabase, $this->userFactory);
    }

    /**
     * Gets a SQL repo to use in this repo
     *
     * @param SQL\Database $sqlDatabase The SQL database used in the repo
     * @return SQLRepo The SQL repo to use
     */
    protected function getSQLRepo(SQL\Database $sqlDatabase)
    {
        return new SQLRepo($sqlDatabase, $this->userFactory);
    }
} 
<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides methods for retrieving user data from the repository
 */
namespace RamODev\Application\Shared\Users\Repositories\User;
use RamODev\Application\Shared\Databases\NoSQL\Redis;
use RamODev\Application\Shared\Databases\SQL;
use RamODev\Application\Shared\Repositories;
use RamODev\Application\Shared\Users;
use RamODev\Application\Shared\Users\Factories;

class Repo extends Repositories\RedisWithPostgreSQLBackupRepo implements IUserRepo
{
    /** @var Factories\IUserFactory The factory to use when creating user objects */
    private $userFactory = null;
    /** @var string The pepper to use before hashing a password */
    private $passwordPepper = "";
    /** @var int The cost of the hash algorithm used to store passwords */
    private $hashCost = 11;

    /**
     * @param Redis\Database $redisDatabase The Redis database used in the repo
     * @param SQL\Database $sqlDatabase The relational database used in the repo
     * @param Factories\IUserFactory $userFactory The user factory to use when creating user objects
     * @param string @passwordPepper The pepper to use before hashing a password
     * @param int $hashCost The cost of the hash algorithm used to store passwords
     */
    public function __construct(Redis\Database $redisDatabase, SQL\Database $sqlDatabase, Factories\IUserFactory $userFactory, $tokenPepper, $hashCost)
    {
        $this->userFactory = $userFactory;
        $this->passwordPepper = $tokenPepper;
        $this->hashCost = $hashCost;

        parent::__construct($redisDatabase, $sqlDatabase);
    }

    /**
     * Adds a user to the repository
     *
     * @param Users\IUser $user The user to store in the repository
     * @param string $password The unhashed password
     * @return bool True if successful, otherwise false
     */
    public function add(Users\IUser &$user, $password = "")
    {
        if(!empty($password))
        {
            $user->setHashedPassword($this->getHashedPassword($password));
        }

        return $this->write(__FUNCTION__, array(&$user, $user->getHashedPassword()));
    }

    /**
     * Gets all the users in the repository
     *
     * @return array|bool The array of users if successful, otherwise false
     */
    public function getAll()
    {
        return $this->read(__FUNCTION__);
    }

    /**
     * Gets the user with the input email
     *
     * @param string $email The email we're searching for
     * @return Users\IUser|bool The user that has the input email if successful, otherwise false
     */
    public function getByEmail($email)
    {
        return $this->read(__FUNCTION__, array($email));
    }

    /**
     * Gets the user with the input Id
     *
     * @param int $id The database Id of the user we're searching for
     * @return Users\IUser|bool The user with the input Id if successful, otherwise false
     */
    public function getById($id)
    {
        return $this->read(__FUNCTION__, array($id));
    }

    /**
     * Gets the user with the input username
     *
     * @param string $username The username to search for
     * @return Users\IUser|bool The user with the input username if successful, otherwise false
     */
    public function getByUsername($username)
    {
        return $this->read(__FUNCTION__, array($username));
    }

    /**
     * Gets the user with the input username and hashed password
     *
     * @param string $username The username to search for
     * @param string $password The unhashed password to search for
     * @return Users\IUser|bool The user with the input username and password if successful, otherwise false
     */
    public function getByUsernameAndPassword($username, $password)
    {
        /**
         * To prevent a person that has gained access to the database from having the ability to reverse-engineer salted hashes stored there,
         * we pepper the password in our code.
         */
        return $this->read(__FUNCTION__, array($username, $this->getHashedPassword($password)));
    }

    /**
     * Synchronizes the Redis repository with the PostgreSQL repository
     *
     * @return bool True if successful, otherwise false
     */
    public function sync()
    {
        return $this->redisRepo->flush() && $this->getAll() !== false;
    }

    /**
     * Updates a user's email address in the repository
     *
     * @param Users\IUser $user The user to update in the repository
     * @param string $email The new email address
     * @return bool True if successful, otherwise false
     */
    public function updateEmail(Users\IUser &$user, $email)
    {
        $user->setEmail($email);

        return $this->write(__FUNCTION__, array(&$user, $email));
    }

    /**
     * Updates a user's password in the repository
     *
     * @param Users\IUser $user The user to update in the repository
     * @param string $password The unhashed new password
     * @return bool True if successful, otherwise false
     */
    public function updatePassword(Users\IUser &$user, $password)
    {
        $user->setHashedPassword($password);

        return $this->write(__FUNCTION__, array(&$user, $password));
    }

    /**
     * Stores a user object that wasn't initially found in the Redis repo
     *
     * @param Users\IUser $user The user to store in the Redis repo
     * @param array $funcArgs The array of function arguments to pass into the method that adds the data to the Redis repo
     */
    protected function addDataToRedisRepo(&$user, $funcArgs = array())
    {
        $this->redisRepo->add($user, $funcArgs);
    }

    /**
     * Gets a SQL repo to use in this repo
     *
     * @param SQL\Database $sqlDatabase The SQL database used in the repo
     * @return PostgreSQLRepo The SQL repo to use
     */
    protected function getPostgreSQLRepo(SQL\Database $sqlDatabase)
    {
        return new PostgreSQLRepo($sqlDatabase, $this->userFactory);
    }

    /**
     * Gets a Redis repo to use in this repo
     *
     * @param Redis\Database $redisDatabase The Redis database used in the repo
     * @return RedisRepo The Redis repo to use
     */
    protected function getRedisRepo(Redis\Database $redisDatabase)
    {
        return new RedisRepo($redisDatabase, $this->userFactory);
    }

    /**
     * Gets the hash of a password, which is suitable for storage
     *
     * @param string $password The unhashed password to hash
     * @return string The hashed password
     */
    private function getHashedPassword($password)
    {
        return password_hash($password . $this->passwordPepper, PASSWORD_BCRYPT, array("cost" => $this->hashCost));
    }
} 
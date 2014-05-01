<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides methods for retrieving user data from a Redis database
 */
namespace RamODev\Application\Shared\Users\Repositories\User;
use RamODev\Application\Shared\Databases\NoSQL\Redis;
use RamODev\Application\Shared\Repositories;
use RamODev\Application\Shared\Users;
use RamODev\Application\Shared\Users\Factories;

class RedisRepo extends Repositories\RedisRepo implements IUserRepo
{
    /** @var Factories\IUserFactory The user factory to use when creating user objects */
    private $userFactory = null;

    /**
     * @param Redis\Database $redisDatabase The database to use for queries
     * @param Factories\IUserFactory $userFactory The factory to use when creating user objects
     */
    public function __construct(Redis\Database $redisDatabase, Factories\IUserFactory $userFactory)
    {
        parent::__construct($redisDatabase);

        $this->userFactory = $userFactory;
    }

    /**
     * Adds a user to the repository
     *
     * @param Users\IUser $user The user to store in the repository
     * @param string $password The hashed password
     * @return bool True if successful, otherwise false
     */
    public function add(Users\IUser &$user, $password)
    {
        $this->storeHashOfUser($user, $password);
        // Add to the user to the users' set
        $this->redisDatabase->getPHPRedis()->sAdd("users", $user->getId());
        // Create the email index
        $this->redisDatabase->getPHPRedis()->set("users:email:" . strtolower($user->getEmail()), $user->getId());
        // Create the username index
        $this->redisDatabase->getPHPRedis()->set("users:username:" . strtolower($user->getUsername()), $user->getId());
    }

    /**
     * Flushes items in this repo
     *
     * @return bool True if successful, otherwise false
     */
    public function flush()
    {
        return $this->redisDatabase->getPHPRedis()->del(array("users")) !== false && $this->redisDatabase->deleteKeyPatterns(array(
            "users:*",
            "users:email:*",
            "users:username:*"
        ));
    }

    /**
     * Gets all the users in the repository
     *
     * @return array|bool The array of users if successful, otherwise false
     */
    public function getAll()
    {
        return $this->read("users", "createUserFromId", false);
    }

    /**
     * Gets the user with the input email
     *
     * @param string $email The email we're searching for
     * @return Users\IUser|bool The user that has the input email if successful, otherwise false
     */
    public function getByEmail($email)
    {
        return $this->read("users:email:" . strtolower($email), "createUserFromId", true);
    }

    /**
     * Gets the user with the input Id
     *
     * @param int $id The database Id of the user we're searching for
     * @return Users\IUser|bool The user with the input Id if successful, otherwise false
     */
    public function getById($id)
    {
        return $this->createUserFromId($id);
    }

    /**
     * Gets the user with the input username
     *
     * @param string $username The username to search for
     * @return Users\IUser|bool The user with the input username if successful, otherwise false
     */
    public function getByUsername($username)
    {
        return $this->read("users:username:" . strtolower($username), "createUserFromId", true);
    }

    /**
     * Gets the user with the input username and hashed password
     *
     * @param string $username The username to search for
     * @param string $unhashedPassword The unhashed password to search for
     * @return Users\IUser|bool The user with the input username and password if successful, otherwise false
     */
    public function getByUsernameAndPassword($username, $unhashedPassword)
    {
        $userFromUsername = $this->getByUsername($username);

        if($userFromUsername === false)
        {
            return false;
        }

        $hashedPassword = $this->redisDatabase->getPHPRedis()->hGet("users:" . $userFromUsername->getId(), "password");

        if($hashedPassword === false || !password_verify($unhashedPassword, $hashedPassword))
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
     */
    public function updateEmail(Users\IUser &$user, $email)
    {
        return $this->update($user->getId(), "email", $email);
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
        return $this->update($user->getId(), "password", $password);
    }

    /**
     * Creates a user object from cache using an Id
     *
     * @param int|string $userId The Id of the user to create
     * @return Users\IUser|bool The user object if successful, otherwise false
     */
    protected function createUserFromId($userId)
    {
        // Cast to int just in case it is still in string-form, which is how Redis stores most data
        $userId = (int)$userId;
        $userHash = $this->redisDatabase->getPHPRedis()->hGetAll("users:" . $userId);

        if($userHash == array())
        {
            return false;
        }

        return $this->userFactory->createUser(
            (int)$userHash["id"],
            $userHash["username"],
            $userHash["email"],
            \DateTime::createFromFormat("U", $userHash["datecreated"], new \DateTimeZone("UTC")),
            $userHash["firstname"],
            $userHash["lastname"]
        );
    }

    /**
     * Stores a hash of a user object in cache
     *
     * @param Users\IUser $user The user object from which we're creating a hash
     * @param string $password The hashed password
     * @return bool True if successful, otherwise false
     */
    private function storeHashOfUser(Users\IUser $user, $password)
    {
        return $this->redisDatabase->getPHPRedis()->hMset("users:" . $user->getId(), array(
            "id" => $user->getId(),
            "password" => $password,
            "username" => $user->getUsername(),
            "email" => $user->getEmail(),
            "lastname" => $user->getLastName(),
            "firstname" => $user->getFirstName(),
            "datecreated" => $user->getDateCreated()->getTimestamp()
        ));
    }

    /**
     * Updates a hash value for a user object in cache
     *
     * @param int $userId The Id of the user we are updating
     * @param string $hashKey They key of the hash property we're updating
     * @param mixed $value The value to write to the hash key
     * @return bool True if successful, otherwise false
     */
    private function update($userId, $hashKey, $value)
    {
        return $this->redisDatabase->getPHPRedis()->hSet("users:" . $userId, $hashKey, $value) !== false;
    }
} 
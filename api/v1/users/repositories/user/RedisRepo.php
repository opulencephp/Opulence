<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides methods for retrieving user data from a Redis database
 */
namespace RamODev\API\V1\Users\Repositories\User;
use RamODev\API\V1\Users;
use RamODev\API\V1\Users\Factories;
use RamODev\Databases\NoSQL\Redis;
use RamODev\Repositories;

require_once(__DIR__ . "/../../../../../repositories/RedisRepo.php");

class RedisRepo extends Repositories\RedisRepo implements IUserRepo
{
    /** @var Factories\IUserFactory The user factory to use when creating user objects */
    private $userFactory = null;

    /**
     * @param Redis\Database $redisDatabase The database to use for queries
     * @param Factories\IUserFactory $userFactory The user factory to use when creating user objects
     */
    public function __construct(Redis\Database $redisDatabase, Factories\IUserFactory $userFactory)
    {
        parent::__construct($redisDatabase);

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
        // Store the user's data as a hash
        $userKey = "users:" . $user->getID();
        $this->redisDatabase->getPHPRedis()->hSetNx($userKey, "id", $user->getID());
        $this->redisDatabase->getPHPRedis()->hSetNx($userKey, "password", $user->getHashedPassword());
        $this->redisDatabase->getPHPRedis()->hSetNx($userKey, "username", $user->getUsername());
        $this->redisDatabase->getPHPRedis()->hSetNx($userKey, "email", $user->getEmail());
        $this->redisDatabase->getPHPRedis()->hSetNx($userKey, "lastname", $user->getLastName());
        $this->redisDatabase->getPHPRedis()->hSetNx($userKey, "firstname", $user->getFirstName());
        $this->redisDatabase->getPHPRedis()->hSetNx($userKey, "datecreated", $user->getDateCreated()->getTimestamp());

        // Add to the user to the users' set
        $this->redisDatabase->getPHPRedis()->sAdd("users", $user->getID());

        // Create the email index
        $this->redisDatabase->getPHPRedis()->set("users:email:" . strtolower($user->getEmail()), $user->getID());

        // Create the username index
        $this->redisDatabase->getPHPRedis()->set("users:username:" . strtolower($user->getUsername()), $user->getID());

        // Create the password index
        $this->redisDatabase->getPHPRedis()->set("users:password:" . $user->getHashedPassword(), $user->getID());
    }

    /**
     * Gets all the users in the repository
     *
     * @return array|bool The array of users if successful, otherwise false
     */
    public function getAll()
    {
        $userIDs = $this->redisDatabase->getPHPRedis()->sMembers("users");

        if($userIDs == array())
        {
            return false;
        }

        $users = array();

        foreach($userIDs as $userID)
        {
            $userHash = $this->redisDatabase->getPHPRedis()->hGetAll("users:" . $userID);

            if($userHash == array())
            {
                return false;
            }

            $users[] = $this->createUserFromHash($userHash);
        }

        return $users;
    }

    /**
     * Gets the user with the input email
     *
     * @param string $email The email we're searching for
     * @return Users\IUser|bool The user that has the input email if successful, otherwise false
     */
    public function getByEmail($email)
    {
        $userID = $this->redisDatabase->getPHPRedis()->get("users:email:" . strtolower($email));

        if($userID === false)
        {
            return false;
        }

        $userHash = $this->redisDatabase->getPHPRedis()->hGetAll("users:" . $userID);

        if($userHash == array())
        {
            return false;
        }

        return $this->createUserFromHash($userHash);
    }

    /**
     * Gets the user with the input ID
     *
     * @param int $id The ID of the user we're searching for
     * @return Users\IUser|bool The user with the input ID if successful, otherwise false
     */
    public function getByID($id)
    {
        $userHash = $this->redisDatabase->getPHPRedis()->hGetAll("users:" . $id);

        if($userHash == array())
        {
            return false;
        }

        return $this->createUserFromHash($userHash);
    }

    /**
     * Gets the user with the input username
     *
     * @param string $username The username to search for
     * @return Users\IUser|bool The user with the input username if successful, otherwise false
     */
    public function getByUsername($username)
    {
        $userID = $this->redisDatabase->getPHPRedis()->get("users:username:" . strtolower($username));

        if($userID === false)
        {
            return false;
        }

        $userHash = $this->redisDatabase->getPHPRedis()->hGetAll("users:" . $userID);

        if($userHash == array())
        {
            return false;
        }

        return $this->createUserFromHash($userHash);
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
        $userIDFromUsername = $this->redisDatabase->getPHPRedis()->get("users:username:" . strtolower($username));

        if($userIDFromUsername === false)
        {
            return false;
        }

        $userIDFromPassword = $this->redisDatabase->getPHPRedis()->get("users:password:" . $hashedPassword);

        // Make sure the user ID from the username matches that of the password
        if($userIDFromPassword === false || $userIDFromUsername !== $userIDFromPassword)
        {
            return false;
        }

        $userHash = $this->redisDatabase->getPHPRedis()->hGetAll("users:" . $userIDFromUsername);

        if($userHash == array())
        {
            return false;
        }

        return $this->createUserFromHash($userHash);
    }

    /**
     * Updates a user in the repository
     *
     * @param Users\IUser $user The user to update in the repository
     * @return bool True if successful, otherwise false
     */
    public function update(Users\IUser &$user)
    {
        // Store the user's data as a hash
        $userKey = "users:" . $user->getID();
        $this->redisDatabase->getPHPRedis()->hSet($userKey, "id", $user->getID());
        $this->redisDatabase->getPHPRedis()->hSet($userKey, "password", $user->getHashedPassword());
        $this->redisDatabase->getPHPRedis()->hSet($userKey, "username", $user->getUsername());
        $this->redisDatabase->getPHPRedis()->hSet($userKey, "email", $user->getEmail());
        $this->redisDatabase->getPHPRedis()->hSet($userKey, "lastname", $user->getLastName());
        $this->redisDatabase->getPHPRedis()->hSet($userKey, "firstname", $user->getFirstName());
        $this->redisDatabase->getPHPRedis()->hSet($userKey, "datecreated", $user->getDateCreated()->getTimestamp());

        // Add to the user to the users' set
        $this->redisDatabase->getPHPRedis()->sAdd("users", $user->getID());

        // Create the email index
        $this->redisDatabase->getPHPRedis()->set("users:email:" . strtolower($user->getEmail()), $user->getID());

        // Create the username index
        $this->redisDatabase->getPHPRedis()->set("users:username:" . strtolower($user->getUsername()), $user->getID());

        // Create the password index
        $this->redisDatabase->getPHPRedis()->set("users:password:" . $user->getHashedPassword(), $user->getID());
    }

    /**
     * Creates a user object from a Redis hash
     *
     * @param array $userHash The Redis hash containing the user's data
     * @return Users\IUser The user object from the input hash
     */
    private function createUserFromHash(array $userHash)
    {
        // Convert from a Unix timestamp
        $dateCreated = new \DateTime(null, new \DateTimeZone("UTC"));
        $dateCreated->setTimestamp($userHash["datecreated"]);

        return $this->userFactory->createUser($userHash["id"], $userHash["username"], $userHash["password"], $userHash["email"], $dateCreated, $userHash["firstname"], $userHash["lastname"]);
    }
} 
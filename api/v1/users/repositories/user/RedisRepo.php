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
     * Adds a user to the repository
     *
     * @param Users\IUser $user The user to store in the repository
     * @return bool True if successful, otherwise false
     */
    public function add(Users\IUser &$user)
    {
        $this->storeHashOfUser($user);
        // Add to the user to the users' set
        $this->redisDatabase->getPHPRedis()->sAdd("users", $user->getID());
        // Create the email index
        $this->redisDatabase->getPHPRedis()->set("users:email:" . strtolower($user->getEmail()), $user->getID());
        // Create the username index
        $this->redisDatabase->getPHPRedis()->set("users:username:" . strtolower($user->getUsername()), $user->getID());
        // Create the password index
        $this->redisDatabase->getPHPRedis()->set("users:password:" . $user->getHashedPassword(), $user->getID());

        $this->addKeyPattern(array("users", "users:email:*", "users:username:*", "users:password:*"));
    }

    /**
     * Flushes items in this repo
     *
     * @return bool True if successful, otherwise false
     */
    public function flush()
    {
        return $this->deleteKeyPatterns();
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

        // Cast all the IDs to int
        $userIDs = array_map("intval", $userIDs);
        $users = array();

        foreach($userIDs as $userID)
        {
            $user = $this->createUserFromID($userID);

            if($user === false)
            {
                return false;
            }

            $users[] = $user;
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

        return $this->createUserFromID((int)$userID);
    }

    /**
     * Gets the user with the input ID
     *
     * @param int $id The ID of the user we're searching for
     * @return Users\IUser|bool The user with the input ID if successful, otherwise false
     */
    public function getByID($id)
    {
        return $this->createUserFromID($id);
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

        return $this->createUserFromID((int)$userID);
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
        if($userIDFromPassword === false || $userIDFromUsername != $userIDFromPassword)
        {
            return false;
        }

        return $this->createUserFromID((int)$userIDFromUsername);
    }

    /**
     * Updates a user in the repository
     *
     * @param Users\IUser $user The user to update in the repository
     * @return bool True if successful, otherwise false
     */
    public function update(Users\IUser &$user)
    {
        $this->add($user);
    }

    /**
     * Creates a user object from cache using an ID
     *
     * @param int $userID The ID of the user to create
     * @return Users\IUser|bool The user object if successful, otherwise false
     */
    private function createUserFromID($userID)
    {
        $userHash = $this->redisDatabase->getPHPRedis()->hGetAll("users:" . $userID);

        if($userHash == array())
        {
            return false;
        }

        return $this->userFactory->createUser((int)$userHash["id"], $userHash["username"], $userHash["password"], $userHash["email"], \DateTime::createFromFormat("U", $userHash["dateCreated"], new \DateTimeZone("UTC")), $userHash["firstName"], $userHash["lastName"]);
    }

    /**
     * Stores a hash of a user object in cache
     *
     * @param Users\IUser $user The user object from which we're creating a hash
     * @return bool True if successful, otherwise false
     */
    private function storeHashOfUser(Users\IUser $user)
    {
        $this->addKeyPattern("users:*");

        return $this->redisDatabase->getPHPRedis()->hMset("users:" . $user->getID(), array(
            "id" => $user->getID(),
            "password" => $user->getHashedPassword(),
            "username" => $user->getUsername(),
            "email" => $user->getEmail(),
            "lastName" => $user->getLastName(),
            "firstName" => $user->getFirstName(),
            "dateCreated" => $user->getDateCreated()->getTimestamp()
        ));
    }
} 
<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides methods for retrieving user data from a Redis database
 */
namespace RamODev\Application\Shared\Models\Users\Repositories\User;
use RamODev\Application\Shared\Models\Cryptography;
use RamODev\Application\Shared\Models\Cryptography\Repositories\Token;
use RamODev\Application\Shared\Models\Cryptography\Repositories\Token\Exceptions\IncorrectHashException;
use RamODev\Application\Shared\Models\Databases\NoSQL\Redis;
use RamODev\Application\Shared\Models\Repositories;
use RamODev\Application\Shared\Models\Users;
use RamODev\Application\Shared\Models\Users\Factories;

class RedisRepo extends Repositories\RedisRepo implements IUserRepo
{
    /** @var Factories\UserFactory The user factory to use when creating user objects */
    private $userFactory = null;
    /** @var Token\ITokenRepo The password token repo */
    private $passwordTokenRepo = null;

    /**
     * @param Redis\Redis $redis The Redis object to use for queries
     * @param Factories\UserFactory $userFactory The factory to use when creating user objects
     * @param Token\ITokenRepo $passwordTokenRepo The password token repo
     */
    public function __construct(Redis\Redis $redis, Factories\UserFactory $userFactory,
                                Token\ITokenRepo $passwordTokenRepo)
    {
        parent::__construct($redis);

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
        $this->storeHashOfUser($user);
        // Add to the user to the users' set
        $this->redis->sAdd("users", $user->getId());
        // Create the email index
        $this->redis->set("users:email:" . strtolower($user->getEmail()), $user->getId());
        // Create the username index
        $this->redis->set("users:username:" . strtolower($user->getUsername()), $user->getId());

        return true;
    }

    /**
     * Flushes items in this repo
     *
     * @return bool True if successful, otherwise false
     */
    public function flush()
    {
        return $this->redis->del(array("users")) !== false
        && $this->redis->deleteKeyPatterns(array(
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
        return $this->read("users", false);
    }

    /**
     * Gets the user with the input email
     *
     * @param string $email The email we're searching for
     * @return Users\IUser|bool The user that has the input email if successful, otherwise false
     */
    public function getByEmail($email)
    {
        return $this->read("users:email:" . strtolower($email), true);
    }

    /**
     * Gets the user with the input username
     *
     * @param string $username The username to search for
     * @return Users\IUser|bool The user with the input username if successful, otherwise false
     */
    public function getByUsername($username)
    {
        return $this->read("users:username:" . strtolower($username), true);
    }

    /**
     * Gets the user with the input username and hashed password
     *
     * @param string $username The username to search for
     * @param string $unhashedPassword The unhashed password to search for
     * @return Users\IUser|bool The user with the input username and password if successful, otherwise false
     * @throws IncorrectHashException Thrown if the unhashed value doesn't match the hashed value
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
     */
    public function updateEmail(Users\IUser &$user, $email)
    {
        return $this->update($user->getId(), "email", $email);
    }

    /**
     * Gets the hash representation of an entity
     *
     * @param int $id The Id of the entity whose hash we're searching for
     * @return array|bool The entity's hash if successful, otherwise false
     */
    protected function getEntityHashById($id)
    {
        return $this->redis->hGetAll("users:" . $id);
    }

    /**
     * Loads an entity from a hash of data
     *
     * @param array $hash The hash of data
     * @return Users\User The entity
     */
    protected function loadEntity(array $hash)
    {
        return $this->userFactory->createUser(
            (int)$hash["id"],
            $hash["username"],
            $hash["email"],
            \DateTime::createFromFormat("U", $hash["datecreated"], new \DateTimeZone("UTC")),
            $hash["firstname"],
            $hash["lastname"]
        );
    }

    /**
     * Stores a hash of a user object in cache
     *
     * @param Users\IUser $user The user object from which we're creating a hash
     * @return bool True if successful, otherwise false
     */
    private function storeHashOfUser(Users\IUser $user)
    {
        return $this->redis->hMset("users:" . $user->getId(), array(
            "id" => $user->getId(),
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
        return $this->redis->hSet("users:" . $userId, $hashKey, $value) !== false;
    }
} 
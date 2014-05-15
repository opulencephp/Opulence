<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides methods for retrieving user data from the repository
 */
namespace RDev\Application\Shared\Models\Users\Repositories\User;
use RDev\Application\Shared\Models\Cryptography;
use RDev\Application\Shared\Models\Cryptography\Repositories\Token;
use RDev\Application\Shared\Models\Cryptography\Repositories\Token\Exceptions\IncorrectHashException;
use RDev\Application\Shared\Models\Databases\NoSQL\Redis;
use RDev\Application\Shared\Models\Databases\SQL;
use RDev\Application\Shared\Models\Repositories;
use RDev\Application\Shared\Models\Users;
use RDev\Application\Shared\Models\Users\Factories;

class Repo extends Repositories\RedisWithPostgreSQLBackupRepo implements IUserRepo
{
    /** @var Factories\UserFactory The factory to use when creating user objects */
    private $userFactory = null;
    /** @var Token\ITokenRepo The password token repo */
    private $passwordTokenRepo = null;

    /**
     * @param Redis\Redis $redis The Redis object used in the repo
     * @param SQL\SQL $sql The SQL object used in the repo
     * @param Factories\UserFactory $userFactory The user factory to use when creating user objects
     * @param Token\ITokenRepo $passwordTokenRepo The password token repo
     */
    public function __construct(Redis\Redis $redis, SQL\SQL $sql, Factories\UserFactory $userFactory,
                                Token\ITokenRepo $passwordTokenRepo)
    {
        $this->userFactory = $userFactory;
        $this->passwordTokenRepo = $passwordTokenRepo;

        parent::__construct($redis, $sql);
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
        // Order here matters because we're counting on Ids getting set before being used in the next method
        $userAddedSuccessfully = $this->write(__FUNCTION__, array(&$user, &$passwordToken, $hashedPassword));

        if($userAddedSuccessfully === false)
        {
            return false;
        }

        $passwordToken->setUserId($user->getId());

        return $this->passwordTokenRepo->add($passwordToken, $hashedPassword);
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
     * @param string $unhashedPassword The unhashed password to search for
     * @return Users\IUser|bool The user with the input username and password if successful, otherwise false
     * @throws IncorrectHashException Thrown if the unhashed value doesn't match the hashed value
     */
    public function getByUsernameAndPassword($username, $unhashedPassword)
    {
        return $this->read(__FUNCTION__, array($username, $unhashedPassword));
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
     * Stores a user object that wasn't initially found in the Redis repo
     *
     * @param Users\IUser $user The user to store in the Redis repo
     * @param array $funcArgs The array of function arguments to pass into the method that adds the data to the Redis repo
     */
    protected function addDataToRedisRepo(&$user, $funcArgs = array())
    {
        $passwordToken = $this->passwordTokenRepo->getByUserId(Cryptography\TokenTypes::PASSWORD, $user->getId());

        if($passwordToken !== false)
        {
            $this->redisRepo->add($user, $passwordToken,
                $this->passwordTokenRepo->getHashedValue($passwordToken->getId()));
        }
    }

    /**
     * Gets a SQL repo to use in this repo
     *
     * @param SQL\SQL $sql The SQL object connection used in the repo
     * @return PostgreSQLRepo The SQL repo to use
     */
    protected function getPostgreSQLRepo(SQL\SQL $sql)
    {
        return new PostgreSQLRepo($sql, $this->userFactory, $this->passwordTokenRepo);
    }

    /**
     * Gets a Redis repo to use in this repo
     *
     * @param Redis\Redis $redis The Redis object connection used in the repo
     * @return RedisRepo The Redis repo to use
     */
    protected function getRedisRepo(Redis\Redis $redis)
    {
        return new RedisRepo($redis, $this->userFactory, $this->passwordTokenRepo);
    }
} 
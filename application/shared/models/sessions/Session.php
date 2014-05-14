<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a session that persists throughout a transaction on a page
 */
namespace RamODev\Application\Shared\Models\Sessions;
use RamODev\Application\Shared\Models\Users;
use RamODev\Application\Shared\Models\Users\Authentication\Credentials;
use RamODev\Application\Shared\Models\Web;

class Session
{
    /** @var Web\HTTP The HTTP object to use in our requests/responses */
    private $http;
    /** @var Users\IUser|null The current user object if there is one, otherwise null */
    private $user = null;
    /** @var Credentials\ICredentials|null The current user's credentials if there are any, otherwise null */
    private $credentials = null;

    /**
     * @param Web\HTTP $http The HTTP object to use in our requests/responses
     */
    public function __construct(Web\HTTP $http)
    {
        $this->http = $http;
    }

    /**
     * @return Credentials\ICredentials|null
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * @return Web\HTTP
     */
    public function getHttp()
    {
        return $this->http;
    }

    /**
     * @return Users\IUser|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param Credentials\ICredentials $credentials
     */
    public function setCredentials(Credentials\ICredentials $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * @param Users\IUser $user
     */
    public function setUser(Users\IUSer $user)
    {
        $this->user = $user;
    }
} 
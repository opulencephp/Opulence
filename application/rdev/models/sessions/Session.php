<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a session that persists throughout a transaction on a page
 */
namespace RDev\Models\Sessions;
use RDev\Models\Users;
use RDev\Models\Users\Authentication\Credentials;
use RDev\Models\Web;

class Session
{
    /** @var Web\HTTPConnection The HTTP connection to use in our requests/responses */
    private $httpConnection;
    /** @var Users\IUser|null The current user object if there is one, otherwise null */
    private $user = null;
    /** @var Credentials\ICredentials|null The current user's credentials if there are any, otherwise null */
    private $credentials = null;

    /**
     * @param Web\HTTPConnection $httpConnection The HTTP connection to use in our requests/responses
     */
    public function __construct(Web\HTTPConnection $httpConnection)
    {
        $this->httpConnection = $httpConnection;
    }

    /**
     * @return Credentials\ICredentials|null
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * @return Web\HTTPConnection
     */
    public function getHTTPConnection()
    {
        return $this->httpConnection;
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
<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a Redis server
 */
namespace RDev\Models\Databases\NoSQL\Redis;
use RDev\Models\Databases;

abstract class Server extends Databases\Server
{
    /** {@inheritdoc} */
    protected $port = 6379;
    /** @var string|null The optional password to use to authenticate the connection */
    protected $password = null;

    /**
     * @return string|null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Gets whether or not the password has been set
     *
     * @return bool True if the password is set, otherwise false
     */
    public function passwordIsSet()
    {
        return $this->password !== null;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }
}
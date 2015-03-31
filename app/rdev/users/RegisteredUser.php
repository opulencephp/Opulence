<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a registered user
 */
namespace RDev\Users;
use DateTime;

class RegisteredUser extends User implements IRegisteredUser
{
    /** @var string The username of the user */
    protected $username = "";

    /**
     * @param int $id The database Id of this user
     * @param string $username The username of the user
     * @param DateTime $dateCreated The date this user was created
     * @param array $roles The list of roles this user has
     */
    public function __construct($id, $username, DateTime $dateCreated, array $roles)
    {
        parent::__construct($id, $dateCreated, $roles);

        $this->username = $username;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }
} 
<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a registered user
 */
namespace RDev\Models\Users;

class RegisteredUser extends User implements IRegisteredUser
{
    /** @var string The hashed password */
    protected $hashedPassword = "";

    /**
     * @param int $id The database Id of this user
     * @param string $username The username of the user
     * @param string $hashedPassword The hashed password
     * @param \DateTime $dateCreated The date this user was created
     * @param array $roles The list of roles this user has
     */
    public function __construct($id, $username, $hashedPassword, \DateTime $dateCreated, array $roles)
    {
        $this->setHashedPassword($hashedPassword);

        parent::__construct($id, $username, $dateCreated, $roles);
    }

    /**
     * {@inheritdoc}
     */
    public function getHashedPassword()
    {
        return $this->hashedPassword;
    }

    /**
     * {@inheritdoc}
     */
    public function setHashedPassword($hashedPassword)
    {
        $this->hashedPassword = $hashedPassword;
    }
} 
<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a registered user
 */
namespace RDev\Models\Users;

class RegisteredUser extends User implements IRegisteredUser
{
    /** @var int|string The password Id */
    protected $passwordId = "";

    /**
     * @param int $id The database Id of this user
     * @param string $username The username of the user
     * @param int|string $passwordId The password Id
     * @param \DateTime $dateCreated The date this user was created
     * @param array $roles The list of roles this user has
     */
    public function __construct($id, $username, $passwordId, \DateTime $dateCreated, array $roles)
    {
        $this->setPasswordId($passwordId);

        parent::__construct($id, $username, $dateCreated, $roles);
    }

    /**
     * {@inheritdoc}
     */
    public function getPasswordId()
    {
        return $this->passwordId;
    }

    /**
     * {@inheritdoc}
     */
    public function setPasswordId($passwordId)
    {
        $this->passwordId = $passwordId;
    }
} 
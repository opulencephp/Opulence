<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks a user object for use in testing
 */
namespace RDev\Tests\Views\Pages\Mocks;

class User
{
    /** @var int The user Id */
    private $id = -1;
    /** @var string The username */
    private $username = "";

    /**
     * @param int $id The user Id
     * @param string $username The username
     */
    public function __construct($id, $username)
    {
        $this->id = $id;
        $this->username = $username;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
} 
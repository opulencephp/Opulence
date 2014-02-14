<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the user interface
 */
namespace RamODev\API\V1\Users;

interface IUser
{
    public function getEmail();

    public function getFirstName();

    public function getPassword();

    public function getID();

    public function getLastName();
} 
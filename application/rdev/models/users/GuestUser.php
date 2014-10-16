<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a guest user
 */
namespace RDev\Models\Users;

class GuestUser extends User
{
    public function __construct()
    {
        parent::__construct(-1, "Guest", new \DateTime("now"), []);
    }
} 
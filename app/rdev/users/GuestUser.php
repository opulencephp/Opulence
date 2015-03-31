<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a guest user
 */
namespace RDev\Users;
use DateTime;

class GuestUser extends User
{
    public function __construct()
    {
        parent::__construct(-1, new DateTime("now"), []);
    }
} 
<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the different types of login results
 */
namespace RDev\Users\Authentication\Controllers;

class LoginResults
{
    /** A valid user was found with the input credentials */
    const SUCCESSFUL = 1;
    /** No user was found with the input credentials */
    const USER_NOT_FOUND = 2;
    /** The user was found, but is not active */
    const USER_DEACTIVATED = 3;
    /** There was an exception trying to find the user */
    const EXCEPTION = 4;
} 
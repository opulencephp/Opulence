<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the different types of credentials
 */
namespace RDev\Models\Authentication\Credentials;

class CredentialTypes
{
    /** A password */
    const PASSWORD = 1;
    /** A login credential */
    const LOGIN = 2;
    /** A credential used to authenticate the identity of an entity, but not log it in */
    const AUTHENTICATION = 3;
} 
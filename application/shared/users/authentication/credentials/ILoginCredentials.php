<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for login credentials to implement
 */
namespace RamODev\Application\Shared\Users\Authentication\Credentials;
use RamODev\Application\Shared\Cryptography;

interface ILoginCredentials extends ICredentials
{
    /**
     * @return Cryptography\Token
     */
    public function getLoginToken();

    /**
     * @param Cryptography\Token $token
     */
    public function setLoginToken(Cryptography\Token $token);
} 
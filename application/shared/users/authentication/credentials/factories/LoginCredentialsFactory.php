<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the login credentials factory
 */
namespace RamODev\Application\Shared\Users\Authentication\Credentials\Factories;
use RamODev\Application\Shared\Cryptography\Factories;
use RamODev\Application\Shared\Users\Authentication\Credentials;

class LoginCredentialsFactory implements ILoginCredentialsFactory
{
    /** The length of the token to use */
    const TOKEN_LENGTH = 64;

    /** @var Factories\ITokenFactory The factory to use to generate tokens */
    private $tokenFactory = null;

    /**
     * @param Factories\ITokenFactory $tokenFactory The factory to use to generate tokens
     */
    public function __construct(Factories\ITokenFactory $tokenFactory)
    {
        $this->tokenFactory = $tokenFactory;
    }

    /**
     * Creates credentials for the input user
     *
     * @param int $userId The Id of the user whose credentials these are
     * @param \DateTime $validFrom The valid-from time
     * @param \DateTime $validTo The valid-to time
     * @return Credentials\LoginCredentials
     */
    public function createLoginCredentials($userId, \DateTime $validFrom, \DateTime $validTo)
    {
        return new Credentials\LoginCredentials($userId,
            $this->tokenFactory->createToken(self::TOKEN_LENGTH, $validFrom, $validTo));
    }
} 
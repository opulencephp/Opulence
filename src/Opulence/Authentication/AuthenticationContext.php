<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication;

/**
 * Defines the current authentication context
 */
class AuthenticationContext implements IAuthenticationContext
{
    /** @var IAuthenticatable|null The current user */
    private $user = null;
    /** @var string The current authentication status */
    private $status = AuthenticationStatusTypes::UNAUTHENTICATED;

    /**
     * @param IAuthenticatable|null $user The current user
     * @param string $status The current authentication status
     */
    public function __construct(
        IAuthenticatable $user = null,
        string $status = AuthenticationStatusTypes::UNAUTHENTICATED
    ) {
        if ($user !== null) {
            $this->setUser($user);
        }

        $this->setStatus($status);
    }

    /**
     * @inheritdoc
     */
    public function getStatus() : string
    {
        return $this->status;
    }

    /**
     * @inheritdoc
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @inheritdoc
     */
    public function isAuthenticated() : bool
    {
        return $this->status === AuthenticationStatusTypes::AUTHENTICATED;
    }

    /**
     * @inheritdoc
     */
    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    /**
     * @inheritdoc
     */
    public function setUser(IAuthenticatable $user)
    {
        $this->user = $user;
    }
}
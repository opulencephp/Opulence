<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication;

use Opulence\Authentication\Users\IUser;

/**
 * Defines the current authentication context
 */
class AuthenticationContext implements IAuthenticationContext
{
    /** @var IUser|null The current user */
    private $user = null;
    /** @var int The current authentication status */
    private $status = AuthenticationStatusTypes::UNAUTHENTICATED;

    /**
     * @param IUser|null $user The current user
     * @param int $status The current authentication status
     */
    public function __construct(IUser $user = null, int $status = AuthenticationStatusTypes::UNAUTHENTICATED)
    {
        if ($user !== null) {
            $this->setUser($user);
        }

        $this->setStatus($status);
    }

    /**
     * @inheritdoc
     */
    public function getStatus() : int
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
    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    /**
     * @inheritdoc
     */
    public function setUser(IUser $user)
    {
        $this->user = $user;
    }
}
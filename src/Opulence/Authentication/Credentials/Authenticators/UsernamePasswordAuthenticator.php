<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authentication\Credentials\Authenticators;

use Opulence\Authentication\Credentials\ICredential;
use Opulence\Authentication\ISubject;
use Opulence\Authentication\Principal;
use Opulence\Authentication\PrincipalTypes;
use Opulence\Authentication\Roles\Orm\IRoleRepository;
use Opulence\Authentication\Subject;
use Opulence\Authentication\Users\IUser;
use Opulence\Authentication\Users\Orm\IUserRepository;

/**
 * Defines the username/password authenticator
 */
class UsernamePasswordAuthenticator implements IAuthenticator
{
    /** @var IUserRepository The user repository */
    protected $userRepository = null;
    /** @var IRoleRepository The role repository */
    protected $roleRepository = null;
    /** @var string The pepper used for hashing */
    protected $pepper = '';

    /**
     * @param IUserRepository $userRepository The user repository
     * @param IRoleRepository $roleRepository The role repository
     * @param string $pepper The pepper used for hashing
     */
    public function __construct(IUserRepository $userRepository, IRoleRepository $roleRepository, string $pepper = '')
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->pepper = $pepper;
    }

    /**
     * @inheritdoc
     */
    public function authenticate(ICredential $credential, ISubject &$subject = null, string &$error = null) : bool
    {
        $username = $credential->getValue('username');
        $password = $credential->getValue('password');

        if ($username === null || $password === null) {
            $error = AuthenticatorErrorTypes::CREDENTIAL_MISSING;

            return false;
        }

        $user = $this->userRepository->getByUsername($username);

        if ($user === null) {
            $error = AuthenticatorErrorTypes::NO_SUBJECT;

            return false;
        }

        if (!\password_verify($password . $this->pepper, $user->getHashedPassword())) {
            $error = AuthenticatorErrorTypes::CREDENTIAL_INCORRECT;

            return false;
        }

        $subject = $this->getSubjectFromUser($user, $credential);

        return true;
    }

    /**
     * Gets a subject from a user
     *
     * @param IUser $user The user
     * @param ICredential $credential The credential
     * @return ISubject The subject
     */
    protected function getSubjectFromUser(IUser $user, ICredential $credential) : ISubject
    {
        $userId = $user->getId();
        $roles = $this->roleRepository->getRoleNamesForSubject($userId);

        return new Subject([new Principal(PrincipalTypes::PRIMARY, $userId, $roles)], [$credential]);
    }
}

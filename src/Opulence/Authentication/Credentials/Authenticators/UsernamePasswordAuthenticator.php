<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Credentials\Authenticators;

use Opulence\Authentication\Credentials\ICredential;
use Opulence\Authentication\ISubject;
use Opulence\Authentication\Principal;
use Opulence\Authentication\PrincipalTypes;
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

    /**
     * @param IUserRepository $userRepository The user repository
     */
    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritdoc
     */
    public function authenticate(ICredential $credential, ISubject &$subject = null) : bool
    {
        $username = $credential->getValue("username");
        $password = $credential->getValue("password");

        if ($username === null || $password === null) {
            return false;
        }

        $user = $this->userRepository->getByUsername($username);

        if ($user === null) {
            return false;
        }

        if (!password_verify($password, $user->getHashedPassword())) {
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
        return new Subject([new Principal(PrincipalTypes::PRIMARY, $user->getId())], [$credential]);
    }
}
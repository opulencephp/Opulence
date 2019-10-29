<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Credentials\Authenticators;

use Opulence\Authentication\Credentials\ICredential;
use Opulence\Authentication\ISubject;
use Opulence\Authentication\Tokens\JsonWebTokens\Orm\IJwtRepository;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\IContextVerifier;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\JwtVerifier;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\VerificationContext;

/**
 * Defines the refresh token authenticator
 */
class RefreshTokenAuthenticator extends JwtAuthenticator
{
    /** @var IJwtRepository The refresh token repository */
    protected IJwtRepository $refreshTokenRepository;

    /**
     * @inheritdoc
     * @param IJwtRepository $refreshTokenRepository
     */
    public function __construct(
        IJwtRepository $refreshTokenRepository,
        IContextVerifier $jwtVerifier,
        VerificationContext $verificationContext
    ) {
        parent::__construct($jwtVerifier, $verificationContext);

        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    /**
     * @inheritdoc
     */
    public function tryAuthenticate(ICredential $credential, ISubject &$subject = null, string &$error = null): bool
    {
        if (!parent::tryAuthenticate($credential, $subject, $error)) {
            return false;
        }

        if (!$this->refreshTokenRepository->has($this->signedJwt)) {
            $error = AuthenticatorErrorTypes::CREDENTIAL_INCORRECT;

            return false;
        }

        return true;
    }
}

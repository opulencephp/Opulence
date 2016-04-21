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
use Opulence\Authentication\Tokens\JsonWebTokens\Orm\IJwtRepository;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\JwtVerifier;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\VerificationContext;

/**
 * Defines the refresh token authenticator
 */
class RefreshTokenAuthenticator extends JwtAuthenticator
{
    /** @var IJwtRepository The refresh token repository */
    protected $refreshTokenRepository = null;

    /**
     * @inheritdoc
     * @param IJwtRepository $refreshTokenRepository
     */
    public function __construct(
        IJwtRepository $refreshTokenRepository,
        JwtVerifier $jwtVerifier,
        VerificationContext $verificationContext
    ) {
        parent::__construct($jwtVerifier, $verificationContext);

        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    /**
     * @inheritdoc
     */
    public function authenticate(ICredential $credential, ISubject &$subject = null, string &$error = null) : bool
    {
        if (!parent::authenticate($credential, $subject, $error)) {
            return false;
        }

        if (!$this->refreshTokenRepository->has($this->signedJwt)) {
            $error = AuthenticatorErrorTypes::CREDENTIAL_INCORRECT;

            return false;
        }

        return true;
    }
}
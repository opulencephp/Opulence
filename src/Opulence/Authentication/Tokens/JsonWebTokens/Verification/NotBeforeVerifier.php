<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens\JsonWebTokens\Verification;

use DateTimeImmutable;
use Opulence\Authentication\Tokens\JsonWebTokens\Jwt;

/**
 * Defines the not-before verifier
 */
class NotBeforeVerifier implements IVerifier
{
    /**
     * @inheritdoc
     */
    public function verify(Jwt $jwt)
    {
        $notBefore = $jwt->getPayload()->getValidFrom();

        if ($notBefore === null) {
            return;
        }

        if ($notBefore > new DateTimeImmutable()) {
            throw new VerificationException("Token cannot be processed yet");
        }
    }
}
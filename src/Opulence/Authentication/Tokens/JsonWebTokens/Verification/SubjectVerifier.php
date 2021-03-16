<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authentication\Tokens\JsonWebTokens\Verification;

use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;

/**
 * Defines the subject verifier
 */
class SubjectVerifier implements IVerifier
{
    /** @var string|null The subject */
    private $subject = null;

    /**
     * @param string|null $subject The subject
     */
    public function __construct(string $subject = null)
    {
        $this->subject = $subject;
    }

    /**
     * @inheritdoc
     */
    public function verify(SignedJwt $jwt, string &$error = null) : bool
    {
        $subject = $jwt->getPayload()->getSubject();

        if ($subject !== $this->subject) {
            $error = JwtErrorTypes::SUBJECT_INVALID;

            return false;
        }

        return true;
    }
}

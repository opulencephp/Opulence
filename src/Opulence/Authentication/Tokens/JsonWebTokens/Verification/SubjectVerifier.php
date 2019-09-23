<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Tokens\JsonWebTokens\Verification;

use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;

/**
 * Defines the subject verifier
 */
final class SubjectVerifier implements IVerifier
{
    /** @var string|null The subject */
    private ?string $subject;

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
    public function verify(SignedJwt $jwt, string &$error = null): bool
    {
        $subject = $jwt->getPayload()->getSubject();

        if ($subject !== $this->subject) {
            $error = JwtErrorTypes::SUBJECT_INVALID;

            return false;
        }

        return true;
    }
}

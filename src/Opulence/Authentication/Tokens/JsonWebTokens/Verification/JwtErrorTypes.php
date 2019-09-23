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

/**
 * Defines the different JWT error types
 */
final class JwtErrorTypes
{
    /** Defines the error that occurs when the audience is invalid */
    public const AUDIENCE_INVALID = 'AUDIENCE_INVALID';
    /** Defines the error that occurs when the token has expired */
    public const EXPIRED = 'EXPIRED';
    /** Defines the error that occurs when the issuer is invalid */
    public const ISSUER_INVALID = 'ISSUER_INVALID';
    /** Defines the error that occurs when the token has not been activated yet */
    public const NOT_ACTIVATED = 'NOT_ACTIVATED';
    /** Defines the error that occurs when the token signature does not match the payload */
    public const SIGNATURE_INCORRECT = 'SIGNATURE_INCORRECT';
    /** Defines the error that occurs when the token signature algorithm is incorrect */
    public const SIGNATURE_ALGORITHM_MISMATCH = 'SIGNATURE_ALGORITHM_MISMATCH';
    /** Defines the error that occurs when the subject is invalid */
    public const SUBJECT_INVALID = 'SUBJECT_INVALID';
}

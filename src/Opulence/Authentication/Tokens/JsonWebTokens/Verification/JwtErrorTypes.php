<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Authentication\Tokens\JsonWebTokens\Verification;

/**
 * Defines the different JWT error types
 */
class JwtErrorTypes
{
    /** Defines the error that occurs when the audience is invalid */
    const AUDIENCE_INVALID = 'AUDIENCE_INVALID';
    /** Defines the error that occurs when the token has expired */
    const EXPIRED = 'EXPIRED';
    /** Defines the error that occurs when the issuer is invalid */
    const ISSUER_INVALID = 'ISSUER_INVALID';
    /** Defines the error that occurs when the token has not been activated yet */
    const NOT_ACTIVATED = 'NOT_ACTIVATED';
    /** Defines the error that occurs when the token signature does not match the payload */
    const SIGNATURE_INCORRECT = 'SIGNATURE_INCORRECT';
    /** Defines the error that occurs when the token signature algorithm is incorrect */
    const SIGNATURE_ALGORITHM_MISMATCH = 'SIGNATURE_ALGORITHM_MISMATCH';
    /** Defines the error that occurs when the subject is invalid */
    const SUBJECT_INVALID = 'SUBJECT_INVALID';
}

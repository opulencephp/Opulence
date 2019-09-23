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

/**
 * Defines the different authenticator error types
 */
final class AuthenticatorErrorTypes
{
    /** Defines the error that occurs when a credential value is expired */
    public const CREDENTIAL_EXPIRED = 'CREDENTIAL_EXPIRED';
    /** Defines the error that occurs when a credential value is incorrect */
    public const CREDENTIAL_INCORRECT = 'CREDENTIAL_INCORRECT';
    /** Defines the error that occurs when a credential is missing a value */
    public const CREDENTIAL_MISSING = 'CREDENTIAL_MISSING';
    /** Defines the error that occurs when there's no subject for the credential */
    public const NO_SUBJECT = 'NO_SUBJECT';
}

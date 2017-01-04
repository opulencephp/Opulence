<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Credentials\Authenticators;

/**
 * Defines the different authenticator error types
 */
class AuthenticatorErrorTypes
{
    /** Defines the error that occurs when a credential value is expired */
    const CREDENTIAL_EXPIRED = "CREDENTIAL_EXPIRED";
    /** Defines the error that occurs when a credential value is incorrect */
    const CREDENTIAL_INCORRECT = "CREDENTIAL_INCORRECT";
    /** Defines the error that occurs when a credential is missing a value */
    const CREDENTIAL_MISSING = "CREDENTIAL_MISSING";
    /** Defines the error that occurs when there's no subject for the credential */
    const NO_SUBJECT = "NO_SUBJECT";
}

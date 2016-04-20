<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Credentials;

/**
 * Defines the different credential types
 */
class CredentialTypes
{
    /** A username/password credential */
    const USERNAME_PASSWORD = "USERNAME_PASSWORD";
    /** An JWT access token credential */
    const JWT_ACCESS_TOKEN = "JWT_ACCESS_TOKEN";
    /** An JWT refresh token credential */
    const JWT_REFRESH_TOKEN = "JWT_REFRESH_TOKEN";
}
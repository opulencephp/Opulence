<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Credentials;

/**
 * Defines the different credential types
 */
final class CredentialTypes
{
    /** A username/password credential */
    public const USERNAME_PASSWORD = 'USERNAME_PASSWORD';
    /** An JWT access token credential */
    public const JWT_ACCESS_TOKEN = 'JWT_ACCESS_TOKEN';
    /** An JWT refresh token credential */
    public const JWT_REFRESH_TOKEN = 'JWT_REFRESH_TOKEN';
}

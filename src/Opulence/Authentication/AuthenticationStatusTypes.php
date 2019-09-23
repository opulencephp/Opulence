<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication;

/**
 * Defines the different authentication status types
 */
final class AuthenticationStatusTypes
{
    /** The subject is not authenticated */
    public const UNAUTHENTICATED = 'UNAUTHENTICATED';
    /** The subject is authenticated */
    public const AUTHENTICATED = 'AUTHENTICATED';
}

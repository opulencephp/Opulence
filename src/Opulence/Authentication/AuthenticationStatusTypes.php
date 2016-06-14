<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication;

/**
 * Defines the different authentication status types
 */
class AuthenticationStatusTypes
{
    /** The user is not authenticated */
    const UNAUTHENTICATED = 1;
    /** The user is authenticated */
    const AUTHENTICATED = 2;
}
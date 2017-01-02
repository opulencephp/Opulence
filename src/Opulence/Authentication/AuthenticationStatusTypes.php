<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication;

/**
 * Defines the different authentication status types
 */
class AuthenticationStatusTypes
{
    /** The subject is not authenticated */
    const UNAUTHENTICATED = "UNAUTHENTICATED";
    /** The subject is authenticated */
    const AUTHENTICATED = "AUTHENTICATED";
}